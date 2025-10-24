<?php

declare(strict_types=1);

namespace App\Command;

use App\Contentful\EntryFactory;
use App\Repository\EventRepositoryInterface;
use App\Repository\RepositoryInterface;
use Contentful\Core\Resource\ResourceInterface;
use DateInterval;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Exception;
use InvalidArgumentException;
use JsonException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class CreateEventsOnContentfulCommand extends Command
{
    public const EVENTS_FILE_LOCATION = __DIR__ . '/../../var/data/events.json';
    protected static $defaultName = 'app:create-events-two';

    private EntryFactory $entryFactory;

    private EventRepositoryInterface $eventRepository;

    public function __construct(EventRepositoryInterface $eventRepository, EntryFactory $entryFactory, string $name = null)
    {
        $this->entryFactory = $entryFactory;
        $this->eventRepository = $eventRepository;
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Creates new events on Contentful')
            ->setHelp(sprintf(
                'This command allows you to create new events on Contentful reading the information from ' .
                'a CSV located in %s. Then it exports the events ids in a second CSV located in %s' .
                '. In the future you can use this CSV to delete programmatically the events from contentful.',
                '$importFileName',
                '$exportFileName'
            ))
            ->addOption('dry-run', null, InputOption::VALUE_NONE)
            ->addOption('days', null, InputOption::VALUE_REQUIRED, 'Number of days to generate events for', 0)
            ->addOption('skip', null, InputOption::VALUE_REQUIRED, 'Number of days to skip before generate events', 0);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $configPath = self::EVENTS_FILE_LOCATION;
        if (!is_file($configPath)) {
            $io->error(sprintf('Config file not found: %s', $configPath));
            return Command::FAILURE;
        }

        $days = $input->getOption('days');
        $skip = $input->getOption('skip');
        if (!is_numeric($days) || !is_numeric($skip)) {
            throw new InvalidArgumentException('Parameters days and skip must be numeric');
        }
        $days = max(1, (int)$days);
        $skip = max(1, (int)$skip);
        $tz = new DateTimeZone('Europe/Rome');
        $dryRun = (bool)$input->getOption('dry-run');

        $schedule = $this->loadSchedule($configPath);
        $this->validateSchedule($schedule);

        $today = new DateTimeImmutable('today', $tz);
        $from = $today->add(new DateInterval("P{$skip}D"));
        $until = $today->add(new DateInterval("P{$days}D"));

        $io->section(sprintf('Generating events from %s to %s (TZ %s)', $today->format('Y-m-d'), $until->sub(new DateInterval('P1D'))->format('Y-m-d'), $tz->getName()));

        $existingKeys = array_map(
            fn (ResourceInterface $e) => $this->buildDedupeKey(
                $e[EventRepositoryInterface::CONTENTFUL_RESOURCE_TITLE_FIELD_ID],
                $e[EventRepositoryInterface::CONTENTFUL_RESOURCE_PLACE_FIELD_ID],
                $e[EventRepositoryInterface::CONTENTFUL_RESOURCE_DATE_FIELD_ID]
            ),
            $this->eventRepository->getNextEvents($days)
        );
        $created = 0;
        $skipped = 0;

        $cursor = $from;
        while ($cursor < $until) {
            $weekday = strtolower($cursor->format('l'));

            $items = $schedule[$weekday] ?? [];
            foreach ($items as $item) {
                $name = trim((string)($item['name'] ?? ''));
                $place = trim((string)($item['place'] ?? ''));
                $hourStr = trim((string)($item['hour'] ?? ''));
                if ($name === '' || $place === '' || $hourStr === '') {
                    throw new InvalidArgumentException(sprintf('Invalid entry for %s: name/place/hour are required', $weekday));
                }

                $start = $this->composeDateTime($cursor, $hourStr, $tz);
                $dedupeKey = $this->buildDedupeKey($name, $place, $start);

                if (in_array($dedupeKey, $existingKeys, true)) {
                    $skipped++;
                    $io->writeln(sprintf('<comment>Skip</comment> %s @ %s (%s) — already exists', $name, $place, $start->format(DateTimeInterface::RFC3339)));
                    continue;
                }

                if ($dryRun) {
                    $io->writeln(sprintf('<info>Would create</info> %s @ %s — %s', $name, $place, $start->format(DateTimeInterface::RFC3339)));
                } else {
                    $this->createEvent($name, $place, $start);
                    $io->writeln(sprintf('<info>Created</info> %s @ %s — %s', $name, $place, $start->format(DateTimeInterface::RFC3339)));
                }
                $created++;

                $existingKeys[] = $dedupeKey;
            }

            $cursor = $cursor->add(new DateInterval('P1D'));
        }
        $io->success(sprintf('Done. Created: %d, Skipped (existing): %d', $created, $skipped));

        return Command::SUCCESS;
    }

    private function createEvent(string $title, string $place, DateTimeImmutable $startsAt): void
    {
        $event =
            $this->entryFactory->create(EventRepositoryInterface::CONTENTFUL_ENTITY_TYPE_ID)
                ->setField(EventRepositoryInterface::CONTENTFUL_RESOURCE_TITLE_FIELD_ID, RepositoryInterface::CONTENTFUL_ITALIAN_LOCALE_CODE, $title)
                ->setField(EventRepositoryInterface::CONTENTFUL_RESOURCE_PLACE_FIELD_ID, RepositoryInterface::CONTENTFUL_ITALIAN_LOCALE_CODE, $place)
                ->setField(EventRepositoryInterface::CONTENTFUL_RESOURCE_DATE_FIELD_ID, RepositoryInterface::CONTENTFUL_ITALIAN_LOCALE_CODE, $startsAt->format(DATE_ATOM));

        $this->eventRepository->create($event);
        $event->publish();
    }

    /**
     * @return array<string, array<int, array{name:string,place:string,hour:string}>>
     * @throws JsonException
     */
    private function loadSchedule(string $path): array
    {
        $json = file_get_contents($path);
        if ($json === false) {
            throw new InvalidArgumentException('Unable to read schedule file');
        }
        $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        if (!is_array($data)) {
            throw new InvalidArgumentException('Invalid JSON schedule');
        }

        return $data;
    }


    /**
     * Validate expected structure.
     * @param array<string, mixed> $schedule
     */
    private function validateSchedule(array $schedule): void
    {
        $validKeys = [
            'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'
        ];
        foreach ($schedule as $k => $arr) {
            if (!in_array($k, $validKeys, true)) {
                throw new InvalidArgumentException(sprintf('Unexpected weekday key: %s', $k));
            }
            if (!is_array($arr)) {
                throw new InvalidArgumentException(sprintf('Weekday "%s" must be an array', $k));
            }
            foreach ($arr as $i => $item) {
                if (!is_array($item)) {
                    throw new InvalidArgumentException(sprintf('Entry %s[%d] must be an object', $k, $i));
                }
                foreach (['name', 'place', 'hour'] as $field) {
                    if (!array_key_exists($field, $item)) {
                        throw new InvalidArgumentException(sprintf('Missing "%s" in %s[%d]', $field, $k, $i));
                    }
                }
            }
        }
    }

    /**
     * @throws Exception
     */
    private function composeDateTime(DateTimeImmutable $day, string $hour, DateTimeZone $tz): DateTimeImmutable
    {
        $hour = trim($hour);
        if (!preg_match('/^\d{2}:\d{2}(?::\d{2})?$/', $hour)) {
            throw new InvalidArgumentException(sprintf('Invalid hour format (expected HH:MM or HH:MM:SS): %s', $hour));
        }
        [$h, $m, $s] = array_map('intval', array_pad(explode(':', $hour), 3, '0'));

        return (new DateTimeImmutable($day->format('Y-m-d'), $tz))->setTime($h, $m, $s);
    }

    /**
     * @throws JsonException
     */
    private function buildDedupeKey(string $name, string $place, DateTimeInterface $startsAt): string
    {
        $payload = json_encode([
            'v' => 1,
            'name' => $name,
            'place' => $place,
            'starts_at' => $startsAt->format(DateTimeInterface::RFC3339),
        ], JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES);

        return sha1((string)$payload);
    }
}

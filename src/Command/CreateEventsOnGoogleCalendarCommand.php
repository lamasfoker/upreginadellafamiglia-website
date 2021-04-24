<?php

declare(strict_types=1);

namespace App\Command;

use App\Repository\EventRepositoryInterface;
use App\Repository\GoogleEventCalendarRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class CreateEventsOnGoogleCalendarCommand extends Command
{
    protected static $defaultName = 'app:sync-calendar';

    private EventRepositoryInterface $eventRepository;

    private GoogleEventCalendarRepositoryInterface $googleEventCalendarRepository;

    public function __construct(
        GoogleEventCalendarRepositoryInterface $googleEventCalendarRepository,
        EventRepositoryInterface $eventRepository,
        $name = null
    ) {
        $this->eventRepository = $eventRepository;
        $this->googleEventCalendarRepository = $googleEventCalendarRepository;
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Creates new events on Google Calendar and update old ones')
            ->setHelp('This command allows to sync the future event through Contenful and Google Calendar.' .
            'It updates on Google only the events updated on Contentful the day before')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        foreach ($this->eventRepository->getUpdatedFutureEvents() as $event) {
            $this->googleEventCalendarRepository->save($event);
        }

        return self::SUCCESS;
    }
}

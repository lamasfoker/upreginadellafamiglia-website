<?php

declare(strict_types=1);

namespace App\Command;

use App\Repository\EventRepositoryInterface;
use Contentful\Management\Client;
use Contentful\Management\Proxy\EnvironmentProxy;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class DeleteEventsOnContentfulCommand extends Command
{
    protected static $defaultName = 'app:delete-events';

    private const CSV_FILE_NAME = 'delete-events.csv';

    private const CSV_FILE_FOLDER_LOCATION = __DIR__ . '/../../var/';

    private EventRepositoryInterface $eventRepository;

    public function __construct(EventRepositoryInterface $eventRepository, string $name = null)
    {
        $this->eventRepository = $eventRepository;
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $fileName = realpath(self::CSV_FILE_FOLDER_LOCATION) . DIRECTORY_SEPARATOR . self::CSV_FILE_NAME;
        $this
            ->setDescription('Deletes events on Contentful')
            ->setHelp(sprintf(
                'This command allows you to delete events on Contentful reading their ids from ' .
                'a CSV located in %s.',
                $fileName
            ))
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $fileName = self::CSV_FILE_FOLDER_LOCATION . self::CSV_FILE_NAME;
        $file = fopen($fileName, 'rb');
        if (!$file) {
            $output->writeln('Error during the opening of the file');

            return self::FAILURE;
        }

        for ($index = 1; !feof($file); $index++) {
            $row = fgetcsv($file);
            if (!is_array($row)) {
                $output->writeln(sprintf('There is a problem in your CSV, check the line number %s', $index));

                return self::FAILURE;
            }

            try {
                $this->deleteEvent($row);
            } catch (Exception $exception) {
                $output->writeln(sprintf('There is a problem in your CSV, check the line number %s. ' .
                    'Probably the id does not exist on Contentful.', $index));

                return self::FAILURE;
            }
        }

        fclose($file);
        $output->writeln(sprintf('The process is completed, %s events deleted', $index - 1));

        return self::SUCCESS;
    }

    /**
     * @param array<string> $data
     */
    private function deleteEvent(array $data): void
    {
        $event = $this->eventRepository->getManagementEventById($data[0]);
        $event->unpublish();
        $event->delete();
    }
}

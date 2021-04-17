<?php

declare(strict_types=1);

namespace App\Command;

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

    private EnvironmentProxy $environmentProxy;

    public function __construct(string $spaceId, Client $client, string $name = null)
    {
        $this->environmentProxy = $client->getEnvironmentProxy($spaceId);
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

    private function deleteEvent(array $data): void
    {
        $event = $this->environmentProxy->getEntry($data[0]);
        $event->unpublish();
        $event->delete();
    }
}

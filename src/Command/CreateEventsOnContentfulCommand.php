<?php

declare(strict_types=1);

namespace App\Command;

use App\Contentful\EntryFactory;
use App\Repository\EventRepositoryInterface;
use App\Repository\RepositoryInterface;
use Contentful\Management\Client;
use Contentful\Management\Proxy\EnvironmentProxy;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class CreateEventsOnContentfulCommand extends Command
{
    protected static $defaultName = 'app:create-events';

    private const IMPORT_CSV_FILE_NAME = 'import-events.csv';

    private const EXPORT_CSV_FILE_NAME = 'export-events-%s.csv';

    private const CSV_FILE_FOLDER_LOCATION = __DIR__ . '/../../var/';

    private EntryFactory $entryFactory;

    private EnvironmentProxy $environmentProxy;

    public function __construct(string $spaceId, Client $client, EntryFactory $entryFactory, string $name = null)
    {
        $this->entryFactory = $entryFactory;
        $this->environmentProxy = $client->getEnvironmentProxy($spaceId);
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $importFileName = realpath(self::CSV_FILE_FOLDER_LOCATION) . DIRECTORY_SEPARATOR . self::IMPORT_CSV_FILE_NAME;
        $exportFileName = realpath(self::CSV_FILE_FOLDER_LOCATION) . DIRECTORY_SEPARATOR . sprintf(self::EXPORT_CSV_FILE_NAME, date('Y-m-d'));
        $this
            ->setDescription('Creates new events on Contentful')
            ->setHelp(sprintf(
                'This command allows you to create new events on Contentful reading the information from ' .
                'a CSV located in %s. Then it exports the events ids in a second CSV located in %s' .
                '. In the future you can use this CSV to delete programmatically the events from contentful.',
                $importFileName,
                $exportFileName
            ))
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $importFileName = self::CSV_FILE_FOLDER_LOCATION . DIRECTORY_SEPARATOR . self::IMPORT_CSV_FILE_NAME;
        $exportFileName = self::CSV_FILE_FOLDER_LOCATION . DIRECTORY_SEPARATOR . sprintf(self::EXPORT_CSV_FILE_NAME, date('Y-m-d'));
        $import = fopen($importFileName, 'rb');
        $export = fopen($exportFileName, 'wb');

        if (!$import || !$export) {
            $output->writeln('Error during the opening of the files');

            return self::FAILURE;
        }

        for ($index = 1; !feof($import); $index++) {
            $row = fgetcsv($import);
            if (!is_array($row)) {
                $output->writeln(sprintf('There is a problem in your CSV, check the line number %s', $index));

                return self::FAILURE;
            }
            fputcsv($export, [$this->createEvent($row)]);
        }

        fclose($export);
        fclose($import);
        $output->writeln(sprintf('The process is completed, store %s file for the future deletion', realpath($exportFileName)));

        return self::SUCCESS;
    }

    /**
     * @param array<string> $data
     */
    private function createEvent(array $data): string
    {
        $event =
            $this->entryFactory->create(EventRepositoryInterface::CONTENTFUL_ENTITY_TYPE_ID)
            ->setField(EventRepositoryInterface::CONTENTFUL_RESOURCE_TITLE_FIELD_ID, RepositoryInterface::CONTENTFUL_ITALIAN_LOCALE_CODE, $data[0])
            ->setField(EventRepositoryInterface::CONTENTFUL_RESOURCE_PLACE_FIELD_ID, RepositoryInterface::CONTENTFUL_ITALIAN_LOCALE_CODE, $data[1])
            ->setField(EventRepositoryInterface::CONTENTFUL_RESOURCE_DATE_FIELD_ID, RepositoryInterface::CONTENTFUL_ITALIAN_LOCALE_CODE, $data[2])
        ;

        $this->environmentProxy->create($event);
        $event->publish();

        return $event->getId();
    }
}

<?php

declare(strict_types=1);

namespace App\Service;

use Exception;

final class EventIdResolver
{
    public const EVENT_ASSOCIATION_FILE_LOCATION = __DIR__ . '/../../var/data/event-association.csv';

    /**
     * @throws Exception
     */
    public function resolveCalendarId(string $contentfulId): string
    {
        $file = fopen(self::EVENT_ASSOCIATION_FILE_LOCATION, 'rb');
        if (!$file) {
            throw new Exception('Error during the opening of the file');
        }

        while (!feof($file)) {
            $row = fgetcsv($file);
            if (is_array($row) && $row[0] === $contentfulId) {
                fclose($file);
                return $row[1];
            }
        }

        fclose($file);
        throw new Exception('None google calendar event is associated with the contentful event id provided');
    }

    /**
     * @throws Exception
     */
    public function deleteAssociation(string $contentfulId): void
    {
        $tmpFileLocation = self::EVENT_ASSOCIATION_FILE_LOCATION . 'tmp';
        $file = fopen(self::EVENT_ASSOCIATION_FILE_LOCATION, 'rb');
        $tmpFile = fopen($tmpFileLocation, 'wb');
        if (!$file || !$tmpFile) {
            throw new Exception('Error during the opening of the files');
        }

        while (!feof($file)) {
            $row = fgetcsv($file);
            if (!is_array($row) || $row[0] === $contentfulId) {
                continue;
            }
            fputcsv($tmpFile, $row);
        }

        fclose($file);
        fclose($tmpFile);
        unlink(self::EVENT_ASSOCIATION_FILE_LOCATION);
        rename($tmpFileLocation, self::EVENT_ASSOCIATION_FILE_LOCATION);
    }

    /**
     * @throws Exception
     */
    public function storeAssociation(string $contentfulId, string $calendarId): void
    {
        $file = fopen(self::EVENT_ASSOCIATION_FILE_LOCATION, 'ab+');
        if (!$file) {
            throw new Exception('Error during the opening of the file');
        }
        fputcsv($file, [$contentfulId, $calendarId]);
        fclose($file);
    }
}

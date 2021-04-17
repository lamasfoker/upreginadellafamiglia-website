<?php

declare(strict_types=1);

namespace App\Service;

final class EventIdResolver
{
    public const EVENT_ASSOCIATION_FILE_LOCATION = __DIR__ . '/../../var/data/event-association.csv';

    public function resolveCalendarId(string $contentfulId): ?string
    {
        $file = fopen(self::EVENT_ASSOCIATION_FILE_LOCATION, 'rb');

        while (!feof($file)) {
            $row = fgetcsv($file);
            if (is_array($row) && $row[0] === $contentfulId) {
                fclose($file);
                return $row[1];
            }
        }

        fclose($file);
        return null;
    }

    public function deleteAssociation(string $contentfulId): void
    {
        $tmpFileLocation = self::EVENT_ASSOCIATION_FILE_LOCATION . 'tmp';
        $file = fopen(self::EVENT_ASSOCIATION_FILE_LOCATION, 'rb');
        $tmpFile = fopen($tmpFileLocation, 'wb');

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

    public function storeAssociation(string $contentfulId, string $calendarId): void
    {
        $file = fopen(self::EVENT_ASSOCIATION_FILE_LOCATION, 'ab+');
        fputcsv($file, [$contentfulId, $calendarId]);
        fclose($file);
    }
}

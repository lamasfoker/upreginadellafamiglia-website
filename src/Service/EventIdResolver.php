<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\NotFoundException;
use PDO;

final class EventIdResolver
{
    private string $databaseDsn;

    private string $databaseUser;

    private string $databasePassword;

    private PDO $db;

    public function __construct(string $databaseDsn, string $databaseUser, string $databasePassword)
    {
        $this->databaseDsn = $databaseDsn;
        $this->databaseUser = $databaseUser;
        $this->databasePassword = $databasePassword;
    }

    /**
     * @throws NotFoundException
     */
    public function getGoogleCalendarEventId(string $contentfulEventId): string
    {
        $stmt = $this->getDb()->prepare(
            'SELECT * FROM Event WHERE contentful_event_id = ? LIMIT 1'
        );
        $stmt->execute([$contentfulEventId]);
        $row = $stmt->fetch();
        if (!is_array($row) || !array_key_exists('google_calendar_event_id', $row)) {
            throw new NotFoundException('None google calendar event is associated with the contentful event id provided');
        }

        return $row['google_calendar_event_id'];
    }

    public function deleteAssociation(string $contentfulEventId): void
    {
        $stmt = $this->getDb()->prepare(
            'DELETE FROM Event WHERE contentful_event_id = ?'
        );
        $stmt->execute([$contentfulEventId]);
    }

    public function storeAssociation(string $contentfulEventId, string $googleCalendarEventId): void
    {
        $stmt = $this->getDb()->prepare(
            'INSERT INTO Event (contentful_event_id, google_calendar_event_id) VALUES (?, ?)'
        );
        $stmt->execute([$contentfulEventId, $googleCalendarEventId]);
    }

    private function getDB(): PDO
    {
        if (!isset($this->db)) {
            $this->db = new PDO($this->databaseDsn, $this->databaseUser, $this->databasePassword);
        }

        return $this->db;
    }
}

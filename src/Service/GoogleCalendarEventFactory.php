<?php

declare(strict_types=1);

namespace App\Service;

use Google_Service_Calendar_Event;

final class GoogleCalendarEventFactory
{
    /**
     * @param array<string> $data
     */
    public function create(array $data = []): Google_Service_Calendar_Event
    {
        return new Google_Service_Calendar_Event($data);
    }
}

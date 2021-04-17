<?php

declare(strict_types=1);

namespace App\Service;

use Google_Client;
use Google_Service_Calendar;

final class GoogleCalendarServiceFactory
{
    private const GOOGLE_AUTHENTICATION_KEY_FILE_FOLDER = __DIR__ . '/../../var/data/';

    private string $keysFileName;

    public function __construct(string $keysFileName)
    {
        $this->keysFileName = $keysFileName;
    }

    public function create(): Google_Service_Calendar
    {
        //see: https://stackoverflow.com/questions/26064095/inserting-google-calendar-entries-with-service-account
        $keyFileLocation = self::GOOGLE_AUTHENTICATION_KEY_FILE_FOLDER . $this->keysFileName;
        $client = new Google_Client();
        $client->setAuthConfig($keyFileLocation);
        $client->addScope(Google_Service_Calendar::CALENDAR_EVENTS);

        return new Google_Service_Calendar($client);
    }
}

<?php
declare(strict_types=1);

namespace App\Repository;

use Contentful\Core\Resource\ResourceInterface;
use Google_Service_Calendar_Event;

interface GoogleEventCalendarRepositoryInterface
{
    public function save(ResourceInterface $contentfulEvent): Google_Service_Calendar_Event;

    public function delete(string $contentfulEventId): void;
}

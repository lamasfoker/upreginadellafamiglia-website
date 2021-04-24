<?php

declare(strict_types=1);

namespace App\Repository;

use Contentful\Core\Resource\ResourceInterface;

interface GoogleEventCalendarRepositoryInterface
{
    public function save(ResourceInterface $contentfulEvent): void;

    public function delete(string $contentfulEventId): void;
}

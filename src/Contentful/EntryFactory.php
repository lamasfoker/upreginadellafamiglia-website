<?php

declare(strict_types=1);

namespace App\Contentful;

use Contentful\Management\Resource\Entry;

final class EntryFactory
{
    public function create(string $contentTypeId): Entry
    {
        return new Entry($contentTypeId);
    }
}

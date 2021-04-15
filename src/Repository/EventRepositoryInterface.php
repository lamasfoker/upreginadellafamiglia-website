<?php

declare(strict_types=1);

namespace App\Repository;

use Contentful\Core\Resource\ResourceInterface;

interface EventRepositoryInterface extends RepositoryInterface
{
    public const CONTENTFUL_ENTITY_TYPE_ID = 'evento';

    public const CONTENTFUL_DATE_FIELD_ID = 'fields.data';

    public const CONTENTFUL_RESOURCE_DATE_FIELD_ID = 'data';

    public const CONTENTFUL_RESOURCE_PLACE_FIELD_ID = 'luogo';

    /**
     * @return ResourceInterface[]
     */
    public function getNextEvents(int $days): array;
}

<?php

declare(strict_types=1);

namespace App\Repository;

use Contentful\Core\Resource\ResourceInterface;

interface EventRepositoryInterface extends RepositoryInterface
{
    public const CONTENTFUL_ENTITY_TYPE_ID = 'evento';

    public const CONTENTFUL_DATE_FIELD_ID = 'fields.data';

    public const CONTENTFUL_RECURRING_WEEK_DAY_FIELD_ID = 'fields.ricorrenza';

    public const CONTENTFUL_RESOURCE_DATE_FIELD_ID = 'data';

    public const CONTENTFUL_RESOURCE_RECURRING_WEEK_DAY_FIELD_ID = 'ricorrenza';

    public const CONTENTFUL_RESOURCE_PLACE_FIELD_ID = 'luogo';

    public const CONTENTFUL_RECURRING_WEEK_DAY_FIELD_ID_MAPPING = [
        'Domenica' => '0',
        'Lunedì' => '1',
        'Martedì' => '2',
        'Mercoledì' => '3',
        'Giovedì' => '4',
        'Venerdì' => '5',
        'Sabato' => '6'
    ];

    /**
     * @return ResourceInterface[]
     */
    public function getNextEvents(int $days): array;

    /**
     * @return ResourceInterface[]
     */
    public function getNextRecurringEvents(): array;
}

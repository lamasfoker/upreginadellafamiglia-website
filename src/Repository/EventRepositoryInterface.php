<?php

declare(strict_types=1);

namespace App\Repository;

use Contentful\Core\Resource\ResourceInterface;
use Contentful\Management\Resource\Behavior\CreatableInterface;
use Contentful\Management\Resource\Entry;

interface EventRepositoryInterface extends RepositoryInterface
{
    public const CONTENTFUL_ENTITY_TYPE_ID = 'evento';

    public const CONTENTFUL_DATE_FIELD_ID = 'fields.data';

    public const CONTENTFUL_RESOURCE_TITLE_FIELD_ID = 'titolo';

    public const CONTENTFUL_RESOURCE_DESCRIPTION_FIELD_ID = 'descrizione';

    public const CONTENTFUL_RESOURCE_PLACE_FIELD_ID = 'luogo';

    public const CONTENTFUL_RESOURCE_DATE_FIELD_ID = 'data';

    public const CONTENTFUL_RESOURCE_NEWS_FIELD_ID = 'notizia';

    public const CONTENTFUL_RESOURCE_GOOGLE_CALENDAR_ID_FIELD_ID = 'googleCalendarId';

    /**
     * @return ResourceInterface[]
     */
    public function getNextEvents(int $days): array;

    /**
     * @return ResourceInterface[]
     */
    public function getUpdatedFutureEvents(): array;

    public function getById(string $eventId): ResourceInterface;

    public function addGoogleCalendarId(ResourceInterface $event, string $googleEventId);

    public function create(CreatableInterface $event);

    public function getManagementEventById(string $eventId): Entry;
}

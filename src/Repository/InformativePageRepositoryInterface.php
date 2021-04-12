<?php

declare(strict_types=1);

namespace App\Repository;

use Contentful\Core\Resource\ResourceInterface;

interface InformativePageRepositoryInterface extends RepositoryInterface
{
    public const CONTENTFUL_ENTITY_TYPE_ID = 'informativa';

    public const CONTENTFUL_SLUG_FIELD_ID = 'fields.slug';

    public const CONTENTFUL_RESOURCE_TITLE_FIELD_ID = 'titolo';

    public function getBySlug(string $slug): ?ResourceInterface;
}

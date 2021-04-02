<?php

declare(strict_types=1);

namespace App\Repository;

use Contentful\Core\Resource\EntryInterface;

interface CmsPageRepositoryInterface extends RepositoryInterface
{
    public const CONTENTFUL_ENTITY_TYPE_ID = 'pagina';

    public const CONTENTFUL_SLUG_FIELD_ID = 'fields.slug';

    public function getBySlug(string $slug): ?EntryInterface;
}

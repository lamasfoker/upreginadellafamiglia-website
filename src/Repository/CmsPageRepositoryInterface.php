<?php

declare(strict_types=1);

namespace App\Repository;

use Contentful\Core\Resource\ResourceInterface;

interface CmsPageRepositoryInterface extends RepositoryInterface
{
    public const CONTENTFUL_ENTITY_TYPE_ID = 'pagina';

    public const CONTENTFUL_SLUG_FIELD_ID = 'fields.slug';

    public const CMS_PAGE_FILE_LOCATION = __DIR__ . '/../../var/data/links.json';

    public function getBySlug(string $slug): ?ResourceInterface;
}

<?php

declare(strict_types=1);

namespace App\Repository;

use Contentful\Core\Resource\ResourceInterface;

interface CmsPageRepositoryInterface extends RepositoryInterface
{
    public const CONTENTFUL_ENTITY_TYPE_ID = 'pagina';

    public const CONTENTFUL_SLUG_FIELD_ID = 'fields.slug';

    public const CONTENTFUL_RESOURCE_TITLE_FIELD_ID = 'titolo';

    public const CONTENTFUL_RESOURCE_REFERENT_MAIL_FIELD_ID = 'email';

    public const CMS_PAGE_FILE_LOCATION = __DIR__ . '/../../var/data/links.json';

    public function getBySlug(string $slug): ?ResourceInterface;
}

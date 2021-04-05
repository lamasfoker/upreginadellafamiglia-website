<?php

declare(strict_types=1);

namespace App\Repository;

use Contentful\Core\Resource\ResourceInterface;

interface NewRepositoryInterface extends RepositoryInterface
{
    public const CONTENTFUL_ENTITY_TYPE_ID = 'notizia';

    public const CONTENTFUL_SLUG_FIELD_ID = 'fields.slug';

    public function getBySlug(string $slug): ?ResourceInterface;

    /**
     * @return ResourceInterface[]
     */
    public function getAllPaginated(int $page, int $size = 5): array;
}

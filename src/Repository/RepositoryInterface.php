<?php

declare(strict_types=1);

namespace App\Repository;

interface RepositoryInterface
{
    public const CONTENTFUL_ENTITY_CREATED_AT_FIELD_ID = 'sys.createdAt';

    public const CONTENTFUL_ENTITY_UPDATED_AT_FIELD_ID = 'sys.updatedAt';
}

<?php

declare(strict_types=1);

namespace App\Repository;

interface RepositoryInterface
{
    public const CONTENTFUL_ENTITY_CREATED_AT_FIELD_ID = 'sys.createdAt';

    public const CONTENTFUL_ENTITY_UPDATED_AT_FIELD_ID = 'sys.updatedAt';

    public const CONTENTFUL_GREATER_THAN_OR_EQUAL_TO_OPERATOR = '[gte]';

    public const CONTENTFUL_LESS_THAN_OR_EQUAL_TO_OPERATOR = '[lte]';

    public const CONTENTFUL_EXISTS_OPERATOR = '[exists]';
}

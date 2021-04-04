<?php

declare(strict_types=1);

namespace App\Repository;

use App\Contentful\QueryFactory;
use Contentful\Delivery\Client\ClientInterface;

final class FormRepository implements FormRepositoryInterface
{
    private ClientInterface $client;

    private QueryFactory $queryFactory;

    public function __construct(ClientInterface $client, QueryFactory $queryFactory)
    {
        $this->client = $client;
        $this->queryFactory = $queryFactory;
    }

    /**
     * @inheritDoc
     */
    public function getAll(): array
    {
        $query = $this->queryFactory->create()
            ->setContentType(self::CONTENTFUL_ENTITY_TYPE_ID)
            ->orderBy(self::CONTENTFUL_ENTITY_UPDATED_AT_FIELD_ID, true);

        return $this->client->getEntries($query)->getItems();
    }
}

<?php

declare(strict_types=1);

namespace App\Repository;

use App\Contentful\QueryFactory;
use Contentful\Core\Resource\ResourceInterface;
use Contentful\Delivery\Client\ClientInterface;

final class NewRepository implements NewRepositoryInterface
{
    private ClientInterface $client;

    private QueryFactory $queryFactory;

    public function __construct(ClientInterface $client, QueryFactory $queryFactory)
    {
        $this->client = $client;
        $this->queryFactory = $queryFactory;
    }

    public function getBySlug(string $slug): ?ResourceInterface
    {
        $query = $this->queryFactory->create()
            ->setContentType(self::CONTENTFUL_ENTITY_TYPE_ID)
            ->where(self::CONTENTFUL_SLUG_FIELD_ID, $slug)
            ->orderBy(self::CONTENTFUL_ENTITY_CREATED_AT_FIELD_ID, true)
        ;

        return $this->client->getEntries($query)->getIterator()->current();
    }

    /**
     * @inheritDoc
     */
    public function getAllPaginated(int $page, int $size = 5): array
    {
        $query = $this->queryFactory->create()
            ->setContentType(self::CONTENTFUL_ENTITY_TYPE_ID)
            ->setSkip(($page - 1) * $size)
            ->setLimit($size)
            ->orderBy(self::CONTENTFUL_ENTITY_UPDATED_AT_FIELD_ID, true)
        ;

        return $this->client->getEntries($query)->getItems();
    }

    public function getInHomepageNews(): ?ResourceInterface
    {
        $query = $this->queryFactory->create()
            ->setContentType(self::CONTENTFUL_ENTITY_TYPE_ID)
            ->where(self::CONTENTFUL_IN_HOMEPAGE_FIELD_ID, 'true')
            ->orderBy(self::CONTENTFUL_ENTITY_UPDATED_AT_FIELD_ID, true)
        ;

        return $this->client->getEntries($query)->getIterator()->current();
    }
}

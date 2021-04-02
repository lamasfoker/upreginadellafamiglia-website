<?php

declare(strict_types=1);

namespace App\Repository;

use App\Contentful\QueryFactory;
use Contentful\Core\Resource\EntryInterface;
use Contentful\Delivery\Client\ClientInterface;
use Exception;

final class CmsPageRepository implements CmsPageRepositoryInterface
{
    private ClientInterface $client;

    private QueryFactory $queryFactory;

    public function __construct(ClientInterface $client, QueryFactory $queryFactory)
    {
        $this->client = $client;
        $this->queryFactory = $queryFactory;
    }

    /**
     * @throws Exception
     */
    public function getBySlug(string $slug): ?EntryInterface
    {
        $query = $this->queryFactory->create()
            ->setContentType(self::CONTENTFUL_ENTITY_TYPE_ID)
            ->where(self::CONTENTFUL_SLUG_FIELD_ID, $slug)
            ->orderBy(self::CONTENTFUL_ENTITY_CREATED_AT_FIELD_ID, true)
        ;

        return $this->client->getEntries($query)->getIterator()->current();
    }
}

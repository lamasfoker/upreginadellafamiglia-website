<?php

declare(strict_types=1);

namespace App\Repository;

use App\Contentful\QueryFactory;
use Contentful\Delivery\Client\ClientInterface;

final class EventRepository implements EventRepositoryInterface
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
    public function getNextEvents(int $days): array
    {
        $query = $this->queryFactory->create()
            ->setContentType(self::CONTENTFUL_ENTITY_TYPE_ID)
            ->where(self::CONTENTFUL_DATE_FIELD_ID . self::CONTENTFUL_GREATER_THAN_OR_EQUAL_TO_OPERATOR, date('Y-m-d'))
            ->where(self::CONTENTFUL_DATE_FIELD_ID . self::CONTENTFUL_LESS_THAN_OR_EQUAL_TO_OPERATOR, date('Y-m-d', strtotime('+' . $days . ' day')))
            ->orderBy(self::CONTENTFUL_DATE_FIELD_ID)
        ;

        return $this->client->getEntries($query)->getItems();
    }
}

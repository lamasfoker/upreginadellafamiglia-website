<?php

declare(strict_types=1);

namespace App\Repository;

use App\Contentful\QueryFactory;
use Contentful\Delivery\Client\ClientInterface;
use Psr\Log\LoggerInterface;

final class EventRepository implements EventRepositoryInterface
{
    private ClientInterface $client;

    private QueryFactory $queryFactory;

    private LoggerInterface $logger;

    public function __construct(ClientInterface $client, QueryFactory $queryFactory, LoggerInterface $logger)
    {
        $this->client = $client;
        $this->queryFactory = $queryFactory;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function getNextEvents(int $days): array
    {
        $timestamp = strtotime('+' . $days . ' day');
        if (!is_int($timestamp)) {
            $this->logger->error(sprintf('strtotime() return false, check the parameter $days: %s', $days));
            return [];
        }

        $query = $this->queryFactory->create()
            ->setContentType(self::CONTENTFUL_ENTITY_TYPE_ID)
            ->where(self::CONTENTFUL_DATE_FIELD_ID . self::CONTENTFUL_GREATER_THAN_OR_EQUAL_TO_OPERATOR, date('Y-m-d'))
            ->where(self::CONTENTFUL_DATE_FIELD_ID . self::CONTENTFUL_LESS_THAN_OR_EQUAL_TO_OPERATOR, date('Y-m-d', $timestamp))
            ->where(self::CONTENTFUL_RECURRING_WEEK_DAY_FIELD_ID . self::CONTENTFUL_EXISTS_OPERATOR, 'false')
            ->orderBy(self::CONTENTFUL_DATE_FIELD_ID)
        ;

        return $this->client->getEntries($query)->getItems();
    }

    /**
     * @inheritDoc
     */
    public function getNextRecurringEvents(): array
    {
        $query = $this->queryFactory->create()
            ->setContentType(self::CONTENTFUL_ENTITY_TYPE_ID)
            ->where(self::CONTENTFUL_RECURRING_WEEK_DAY_FIELD_ID . self::CONTENTFUL_EXISTS_OPERATOR, 'true')
        ;

        return $this->client->getEntries($query)->getItems();
    }
}

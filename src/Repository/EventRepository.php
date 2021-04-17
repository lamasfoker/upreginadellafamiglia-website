<?php

declare(strict_types=1);

namespace App\Repository;

use App\Contentful\QueryFactory;
use Contentful\Core\Resource\ResourceInterface;
use Contentful\Delivery\Client\ClientInterface;
use Psr\Log\LoggerInterface;
// DateTime have to be in ISO-8601 format. PHP for ISO-8601 use DATE_ATOM constant
// https://www.contentful.com/developers/docs/references/content-delivery-api/#/reference/search-parameters/order
// https://www.php.net/manual/en/class.datetimeinterface.php#datetime.constants.iso8601

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
        //todo: refactor
        $timestamp = strtotime('+' . $days . ' day');
        if (!is_int($timestamp)) {
            $this->logger->error(sprintf('strtotime() return false, check the parameter $days: %s', $days));
            return [];
        }

        $query = $this->queryFactory->create()
            ->setContentType(self::CONTENTFUL_ENTITY_TYPE_ID)
            ->where(self::CONTENTFUL_DATE_FIELD_ID . self::CONTENTFUL_GREATER_THAN_OR_EQUAL_TO_OPERATOR, date(DATE_ATOM, strtotime('today')))
            ->where(self::CONTENTFUL_DATE_FIELD_ID . self::CONTENTFUL_LESS_THAN_OR_EQUAL_TO_OPERATOR, date(DATE_ATOM, $timestamp))
            ->orderBy(self::CONTENTFUL_DATE_FIELD_ID)
        ;

        return $this->client->getEntries($query)->getItems();
    }

    /**
     * @inheritDoc
     */
    public function getUpdatedFutureEvents(): array
    {
        $query = $this->queryFactory->create()
            ->setContentType(self::CONTENTFUL_ENTITY_TYPE_ID)
            ->where(self::CONTENTFUL_DATE_FIELD_ID . self::CONTENTFUL_GREATER_THAN_OR_EQUAL_TO_OPERATOR, date(DATE_ATOM, strtotime('today')))
            ->where(self::CONTENTFUL_ENTITY_UPDATED_AT_FIELD_ID . self::CONTENTFUL_GREATER_THAN_OR_EQUAL_TO_OPERATOR, date(DATE_ATOM , strtotime('yesterday 6am')))
        ;

        return $this->client->getEntries($query)->getItems();
    }
}

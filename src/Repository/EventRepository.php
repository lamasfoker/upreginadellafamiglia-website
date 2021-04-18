<?php

declare(strict_types=1);

namespace App\Repository;

use App\Contentful\QueryFactory;
use Contentful\Core\Resource\ResourceInterface;
use Contentful\Delivery\Client\ClientInterface;
use Contentful\Management\Client;
use Contentful\Management\Proxy\EnvironmentProxy;
use Contentful\Management\Resource\Behavior\CreatableInterface;
use Contentful\Management\Resource\Entry;
use Exception;

// DateTime have to be in ISO-8601 format. PHP for ISO-8601 use DATE_ATOM constant
// https://www.contentful.com/developers/docs/references/content-delivery-api/#/reference/search-parameters/order
// https://www.php.net/manual/en/class.datetimeinterface.php#datetime.constants.iso8601

final class EventRepository implements EventRepositoryInterface
{
    private ClientInterface $deliveryClient;

    private QueryFactory $queryFactory;

    private EnvironmentProxy $environmentProxy;

    public function __construct(ClientInterface $deliveryClient, QueryFactory $queryFactory, string $spaceId, Client $managementClient)
    {
        $this->deliveryClient = $deliveryClient;
        $this->queryFactory = $queryFactory;
        $this->environmentProxy = $managementClient->getEnvironmentProxy($spaceId);
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function getNextEvents(int $days): array
    {
        $timestamp = strtotime('+' . $days . ' day');
        if (!is_int($timestamp)) {
            throw new Exception(sprintf('strtotime() return false, check the parameter $days: %s', $days));
        }

        $query = $this->queryFactory->create()
            ->setContentType(self::CONTENTFUL_ENTITY_TYPE_ID)
            ->where(self::CONTENTFUL_DATE_FIELD_ID . self::CONTENTFUL_GREATER_THAN_OR_EQUAL_TO_OPERATOR, date(DATE_ATOM, strtotime('today')))
            ->where(self::CONTENTFUL_DATE_FIELD_ID . self::CONTENTFUL_LESS_THAN_OR_EQUAL_TO_OPERATOR, date(DATE_ATOM, $timestamp))
            ->orderBy(self::CONTENTFUL_DATE_FIELD_ID)
        ;

        return $this->deliveryClient->getEntries($query)->getItems();
    }

    /**
     * @inheritDoc
     */
    public function getUpdatedFutureEvents(): array
    {
        $query = $this->queryFactory->create()
            ->setContentType(self::CONTENTFUL_ENTITY_TYPE_ID)
            ->where(self::CONTENTFUL_DATE_FIELD_ID . self::CONTENTFUL_GREATER_THAN_OR_EQUAL_TO_OPERATOR, date(DATE_ATOM, strtotime('today')))
            ->where(self::CONTENTFUL_ENTITY_UPDATED_AT_FIELD_ID . self::CONTENTFUL_GREATER_THAN_OR_EQUAL_TO_OPERATOR, date(DATE_ATOM, strtotime('yesterday 6am')))
        ;

        return $this->deliveryClient->getEntries($query)->getItems();
    }

    public function getById(string $eventId): ResourceInterface
    {
        return $this->deliveryClient->getEntry($eventId);
    }

    public function addGoogleCalendarId(ResourceInterface $event, string $googleEventId): void
    {
        $event = $this->environmentProxy->getEntry($event->getId());

        $isPublished = $event->getSystemProperties()->isPublished();
        $isDraft = $event->getSystemProperties()->isDraft();
        $isUpdated = $event->getSystemProperties()->isUpdated();

        $event
            ->setField(EventRepositoryInterface::CONTENTFUL_RESOURCE_GOOGLE_CALENDAR_ID_FIELD_ID, RepositoryInterface::CONTENTFUL_ITALIAN_LOCALE_CODE, $googleEventId)
            ->update()
        ;

        if ($isPublished && !$isUpdated && !$isDraft) {
            $event->publish();
        }
    }

    public function create(CreatableInterface $event): void
    {
        $this->environmentProxy->create($event);
    }

    public function getManagementEventById(string $eventId): Entry
    {
        return $this->environmentProxy->getEntry($eventId);
    }
}

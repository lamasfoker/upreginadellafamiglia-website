<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\EventRepositoryInterface;
use App\Repository\NewsRepositoryInterface;
use Contentful\Core\Resource\ResourceInterface;
use Contentful\Delivery\Client\ClientInterface;
use DateTime;
use DateTimeInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class Homepage extends AbstractController
{
    private const NUMBER_OF_NEWS_IN_THE_SLIDER = 7;

    private const NUMBER_OF_DAYS_IN_HOMEPAGE_CALENDAR = 7;

    private const WEEKLY_BULLETIN_CONTENTFUL_ENTRY_ID = 'eCUWnHkHZ9hznSf1HFWwW';

    private NewsRepositoryInterface $newsRepository;

    private EventRepositoryInterface $eventRepository;

    private ClientInterface $client;

    public function __construct(
        NewsRepositoryInterface $newsRepository,
        EventRepositoryInterface $eventRepository,
        ClientInterface $client
    ) {
        $this->newsRepository = $newsRepository;
        $this->eventRepository = $eventRepository;
        $this->client = $client;
    }

    public function index(): Response
    {
        return $this->render(
            'homepage/index.html.twig',
            [
                'weeklyBulletin' => $this->client->getEntry(self::WEEKLY_BULLETIN_CONTENTFUL_ENTRY_ID),
                'news' => $this->newsRepository->getInHomepageNews(),
                'slider' => $this->newsRepository->getAllPaginated(1, self::NUMBER_OF_NEWS_IN_THE_SLIDER),
                'days' => $this->groupEventsForDayAndPlace(
                    $this->eventRepository->getNextEvents(self::NUMBER_OF_DAYS_IN_HOMEPAGE_CALENDAR),
                )
            ]
        );
    }

    /**
     * @param array<ResourceInterface> $events
     * @return array<array>
     */
    private function groupEventsForDayAndPlace(array $events): array
    {
        $data = [];
        $places = $this->getPlaces($events);

        for ($day = 0; $day < self::NUMBER_OF_DAYS_IN_HOMEPAGE_CALENDAR; $day++) {
            $data[$day]['date'] = $this->getDateOfTheDayFromToday($day);

            foreach ($places as $currentPlace) {
                $eventsOfCurrentPlaceAndDate = $this->getEventsForCurrentPlaceAndDate($events, $data[$day]['date'], $currentPlace);
                if (count($eventsOfCurrentPlaceAndDate) === 0) {
                    continue;
                }

                $data[$day]['sections'][] = [
                    'place' => $currentPlace,
                    'events' => $eventsOfCurrentPlaceAndDate
                ];
            }
        }

        return $data;
    }

    /**
     * @param array<ResourceInterface> $events
     * @return array<string>
     */
    private function getPlaces(array $events): array
    {
        return array_unique(
            array_map(
                static function (ResourceInterface $event) {
                    return $event[EventRepositoryInterface::CONTENTFUL_RESOURCE_PLACE_FIELD_ID];
                },
                $events
            )
        );
    }

    /**
     * @param ResourceInterface[] $events
     * @return ResourceInterface[]
     */
    private function getEventsForCurrentPlaceAndDate(
        array $events,
        DateTimeInterface $currentDate,
        string $currentPlace
    ): array {
        return array_values(
            array_filter(
                $events,
                static function (ResourceInterface $event) use ($currentDate, $currentPlace) {
                    if ($event[EventRepositoryInterface::CONTENTFUL_RESOURCE_PLACE_FIELD_ID] !== $currentPlace) {
                        return false;
                    }
                    return $event[EventRepositoryInterface::CONTENTFUL_RESOURCE_DATE_FIELD_ID]->format('Y-m-d') === $currentDate->format('Y-m-d');
                }
            )
        );
    }

    private function getDateOfTheDayFromToday(int $day): DateTimeInterface
    {
        return (new DateTime())->modify('+' . $day . ' day');
    }
}

<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\EventRepositoryInterface;
use App\Repository\NewsRepositoryInterface;
use Contentful\Core\Resource\ResourceInterface;
use DateTime;
use DateTimeInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class Homepage extends AbstractController
{
    private const NUMBER_OF_NEWS_IN_THE_SLIDER = 7;

    private const NUMBER_OF_DAYS_IN_HOMEPAGE_CALENDAR = 7;

    private NewsRepositoryInterface $newsRepository;

    private EventRepositoryInterface $eventRepository;

    public function __construct(NewsRepositoryInterface $newsRepository, EventRepositoryInterface $eventRepository)
    {
        $this->newsRepository = $newsRepository;
        $this->eventRepository = $eventRepository;
    }

    public function index(): Response
    {
        return $this->render(
            'homepage/index.html.twig',
            [
                'news' => $this->newsRepository->getInHomepageNews(),
                'slider' => $this->newsRepository->getAllPaginated(1, self::NUMBER_OF_NEWS_IN_THE_SLIDER),
                'days' => $this->groupEventsForDayAndPlace(
                    array_merge(
                        $this->eventRepository->getNextEvents(self::NUMBER_OF_DAYS_IN_HOMEPAGE_CALENDAR),
                        $this->eventRepository->getNextRecurringEvents()
                    )
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
    private function getEventsForCurrentPlaceAndDate(array $events, DateTimeInterface $currentDate, string $currentPlace): array
    {
        return array_values(
            array_filter(
                $events,
                static function (ResourceInterface $event) use ($currentDate, $currentPlace) {
                    if ($event[EventRepositoryInterface::CONTENTFUL_RESOURCE_PLACE_FIELD_ID] !== $currentPlace) {
                        return false;
                    }
                    if ($event[EventRepositoryInterface::CONTENTFUL_RESOURCE_RECURRING_WEEK_DAY_FIELD_ID]) {
                        $recurringWeekDay = $event[EventRepositoryInterface::CONTENTFUL_RESOURCE_RECURRING_WEEK_DAY_FIELD_ID];
                        return
                            array_key_exists($recurringWeekDay, EventRepositoryInterface::CONTENTFUL_RECURRING_WEEK_DAY_FIELD_ID_MAPPING) &&
                            EventRepositoryInterface::CONTENTFUL_RECURRING_WEEK_DAY_FIELD_ID_MAPPING[$recurringWeekDay] === $currentDate->format('w');
                    }
                    return $event[EventRepositoryInterface::CONTENTFUL_RESOURCE_DATE_FIELD_ID]->format('Y-m-d') === $currentDate->format('Y-m-d');
                }
            )
        );
    }

    private function getDateOfTheDayFromToday(int $day): DateTimeInterface
    {
        $date = new DateTime();
        return $date->modify('+' . $day . ' day');
    }
}

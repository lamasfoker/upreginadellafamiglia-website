<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\EventRepositoryInterface;
use App\Repository\NewsRepositoryInterface;
use Contentful\Core\Resource\ResourceInterface;
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
                'days' => $this->groupEventsForDayAndPlace($this->eventRepository->getNextEvents(self::NUMBER_OF_DAYS_IN_HOMEPAGE_CALENDAR))
            ]
        );
    }

    /**
     * @param ResourceInterface[] $events
     * @return array
     */
    private function groupEventsForDayAndPlace(array $events): array
    {
        $data = [];
        $places = $this->getPlaces($events);

        for ($day = 0; $day < self::NUMBER_OF_DAYS_IN_HOMEPAGE_CALENDAR; $day++) {
            $currentDate = date('Y-m-d', strtotime('+' . $day . ' day'));

            foreach ($places as $currentPlace) {
                $eventsOfCurrentPlaceAndDate = $this->getEventsForCurrentPlaceAndDate($events, $currentDate, $currentPlace);
                if (count($eventsOfCurrentPlaceAndDate) === 0) {
                    continue;
                }

                $data[$day]['date'] = $eventsOfCurrentPlaceAndDate[0][EventRepositoryInterface::CONTENTFUL_RESOURCE_DATE_FIELD_ID];
                $data[$day]['sections'][] = [
                    'place' => $currentPlace,
                    'events' => $eventsOfCurrentPlaceAndDate
                ];
            }
        }

        return $data;
    }

    /**
     * @param ResourceInterface[] $events
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
    private function getEventsForCurrentPlaceAndDate(array $events, string $currentDate, string $currentPlace): array
    {
        return array_values(
            array_filter(
                $events,
                static function (ResourceInterface $event) use ($currentDate, $currentPlace) {
                    return
                        $event[EventRepositoryInterface::CONTENTFUL_RESOURCE_DATE_FIELD_ID]->format('Y-m-d') === $currentDate &&
                        $event[EventRepositoryInterface::CONTENTFUL_RESOURCE_PLACE_FIELD_ID] === $currentPlace;
                }
            )
        );
    }
}

<?php

declare(strict_types=1);

namespace App\Repository;

use App\Exception\NotFoundException;
use App\Service\EventIdResolver;
use App\Service\GoogleCalendarEventFactory;
use App\Service\GoogleCalendarServiceFactory;
use Contentful\Core\Resource\ResourceInterface;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use Google_Service_Calendar_EventDateTime;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class GoogleEventCalendarRepository implements GoogleEventCalendarRepositoryInterface
{
    private EventIdResolver $eventIdResolver;

    private Google_Service_Calendar $calendarService;

    private string $calendarId;

    private GoogleCalendarEventFactory $calendarEventFactory;

    private UrlGeneratorInterface $urlGenerator;

    private TranslatorInterface $translator;

    public function __construct(
        EventIdResolver $eventIdResolver,
        GoogleCalendarServiceFactory $calendarServiceFactory,
        string $calendarId,
        GoogleCalendarEventFactory $calendarEventFactory,
        UrlGeneratorInterface $urlGenerator,
        TranslatorInterface $translator
    ) {
        $this->eventIdResolver = $eventIdResolver;
        $this->calendarService = $calendarServiceFactory->create();
        $this->calendarId = $calendarId;
        $this->calendarEventFactory = $calendarEventFactory;
        $this->urlGenerator = $urlGenerator;
        $this->translator = $translator;
    }

    public function save(ResourceInterface $contentfulEvent): void
    {
        try {
            $googleEventId = $this->eventIdResolver->getGoogleCalendarEventId($contentfulEvent->getId());
            $calendarEvent = $this->calendarService->events->get($this->calendarId, $googleEventId);
            $calendarEvent = $this->copyDataFromContentfulToCalendar($contentfulEvent, $calendarEvent);
            $this->calendarService->events->update($this->calendarId, $googleEventId, $calendarEvent);
        } catch (NotFoundException $e) {
            $calendarEvent = $this->calendarEventFactory->create();
            $calendarEvent = $this->copyDataFromContentfulToCalendar($contentfulEvent, $calendarEvent);
            $calendarEvent = $this->calendarService->events->insert($this->calendarId, $calendarEvent);
            $this->eventIdResolver->storeAssociation($contentfulEvent->getId(), $calendarEvent->getId());
        }
    }

    /**
     * @throws NotFoundException
     */
    public function delete(string $contentfulEventId): void
    {
        $calendarEventId = $this->eventIdResolver->getGoogleCalendarEventId($contentfulEventId);
        $this->calendarService->events->delete($this->calendarId, $calendarEventId);
        $this->eventIdResolver->deleteAssociation($contentfulEventId);
    }

    private function copyDataFromContentfulToCalendar(ResourceInterface $contentfulEvent, Google_Service_Calendar_Event $calendarEvent): Google_Service_Calendar_Event
    {
        $calendarEvent->setSummary($contentfulEvent[EventRepositoryInterface::CONTENTFUL_RESOURCE_TITLE_FIELD_ID]);
        $calendarEvent->setLocation($this->formatLocation($contentfulEvent));
        $calendarEvent->setDescription($this->formatDescription($contentfulEvent));
        $calendarEvent->setStart(new Google_Service_Calendar_EventDateTime(
            [
                'dateTime' => $contentfulEvent[EventRepositoryInterface::CONTENTFUL_RESOURCE_DATE_FIELD_ID]->format(DATE_ATOM),
                'timeZone' => $contentfulEvent[EventRepositoryInterface::CONTENTFUL_RESOURCE_DATE_FIELD_ID]->getTimeZone()->getName(),
            ]
        ));
        $calendarEvent->setEnd(new Google_Service_Calendar_EventDateTime(
            [
                'dateTime' => $contentfulEvent[EventRepositoryInterface::CONTENTFUL_RESOURCE_DATE_FIELD_ID]->modify('+ 1 hour')->format(DATE_ATOM),
                'timeZone' => $contentfulEvent[EventRepositoryInterface::CONTENTFUL_RESOURCE_DATE_FIELD_ID]->getTimeZone()->getName(),
            ]
        ));

        return $calendarEvent;
    }

    private function formatDescription(ResourceInterface $contentfulEvent): string
    {
        $description = '';
        if ($contentfulEvent[EventRepositoryInterface::CONTENTFUL_RESOURCE_DESCRIPTION_FIELD_ID]) {
            $description = $contentfulEvent[EventRepositoryInterface::CONTENTFUL_RESOURCE_DESCRIPTION_FIELD_ID] . '<br/>';
        }
        if ($contentfulEvent[EventRepositoryInterface::CONTENTFUL_RESOURCE_NEWS_FIELD_ID]) {
            $description .= sprintf(
                $this->translator->trans('app.calendar.link'),
                $this->generateNewsUrl($contentfulEvent[EventRepositoryInterface::CONTENTFUL_RESOURCE_NEWS_FIELD_ID])
            );
        }

        return $description;
    }

    private function formatLocation(ResourceInterface $contentfulEvent): string
    {
        return $this->translator->trans('app.calendar.location', ['%location%' => $contentfulEvent[EventRepositoryInterface::CONTENTFUL_RESOURCE_PLACE_FIELD_ID]]);
    }

    private function generateNewsUrl(ResourceInterface $news): string
    {
        return $this->urlGenerator->generate(
            'news_index',
            ['slug' => $news[NewsRepositoryInterface::CONTENTFUL_RESOURCE_SLUG_FIELD_ID]],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
    }
}

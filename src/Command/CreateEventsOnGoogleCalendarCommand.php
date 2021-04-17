<?php
declare(strict_types=1);

namespace App\Command;

use App\Repository\EventRepositoryInterface;
use App\Repository\NewsRepositoryInterface;
use App\Repository\RepositoryInterface;
use App\Service\GoogleCalendarEventFactory;
use App\Service\GoogleCalendarServiceFactory;
use Contentful\Core\Resource\ResourceInterface;
use Contentful\Management\Client;
use Contentful\Management\Proxy\EnvironmentProxy;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use Google_Service_Calendar_EventDateTime;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class CreateEventsOnGoogleCalendarCommand extends Command
{
    protected static $defaultName = 'app:sync-calendar';

    private Google_Service_Calendar $calendarService;

    private string $calendarId;

    private GoogleCalendarEventFactory $calendarEventFactory;

    private EventRepositoryInterface $eventRepository;

    private UrlGeneratorInterface $urlGenerator;

    private TranslatorInterface $translator;

    private EnvironmentProxy $environmentProxy;

    public function __construct(
        Client $client,
        string $spaceId,
        TranslatorInterface $translator,
        UrlGeneratorInterface $urlGenerator,
        EventRepositoryInterface $eventRepository,
        GoogleCalendarEventFactory $calendarEventFactory,
        GoogleCalendarServiceFactory $calendarServiceFactory,
        string $calendarId,
        $name = null
    ) {
        $this->environmentProxy = $client->getEnvironmentProxy($spaceId);
        $this->calendarService = $calendarServiceFactory->create();
        $this->calendarId = $calendarId;
        $this->calendarEventFactory = $calendarEventFactory;
        $this->eventRepository = $eventRepository;
        $this->urlGenerator = $urlGenerator;
        $this->translator = $translator;
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Creates new events on Google Calendar and update old ones')
            ->setHelp('This command allows to sync the future event through Contenful and Google Calendar.' .
            'It updates on Google only the events updated on Contentful the day before')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        foreach ($this->eventRepository->getUpdatedFutureEvents() as $event) {
            $googleEventId = $event[EventRepositoryInterface::CONTENTFUL_RESOURCE_GOOGLE_CALENDAR_ID_FIELD_ID];
            if ($googleEventId) {
                $this->updateEventOnCalendar($event);
            } else {
                $googleEventId = $this->createEventOnCalendar($event);
                $this->addGoogleCalendarIdInContentfulEvent($event->getId(), $googleEventId);
            }
        }

        return self::SUCCESS;
    }

    private function createEventOnCalendar(ResourceInterface $contentfulEvent): string
    {
        $calendarEvent = $this->calendarEventFactory->create();
        $calendarEvent = $this->copyDataFromContentfulToCalendar($contentfulEvent, $calendarEvent);
        $calendarEvent = $this->calendarService->events->insert($this->calendarId, $calendarEvent);

        return $calendarEvent->getId();
    }

    private function updateEventOnCalendar(ResourceInterface $contentfulEvent): void
    {
        $googleEventId = $contentfulEvent[EventRepositoryInterface::CONTENTFUL_RESOURCE_GOOGLE_CALENDAR_ID_FIELD_ID];
        if (!$googleEventId) {
            return;
        }
        $calendarEvent = $this->calendarService->events->get($this->calendarId, $googleEventId);
        $calendarEvent = $this->copyDataFromContentfulToCalendar($contentfulEvent, $calendarEvent);
        $this->calendarService->events->update($this->calendarId, $googleEventId, $calendarEvent);
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

    private function generateNewsUrl(ResourceInterface $news): string
    {
        return $this->urlGenerator->generate(
            'news_index',
            ['slug' => $news[NewsRepositoryInterface::CONTENTFUL_RESOURCE_SLUG_FIELD_ID]],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
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

    private function addGoogleCalendarIdInContentfulEvent(string $eventId, string $googleEventId): void
    {
        $event = $this->environmentProxy->getEntry($eventId);

        $isPublished = $event->getSystemProperties()->isPublished();
        $isDraft = $event->getSystemProperties()->isDraft();
        $isUpdated = $event->getSystemProperties()->isUpdated();

        $event
            ->setField(EventRepositoryInterface::CONTENTFUL_RESOURCE_GOOGLE_CALENDAR_ID_FIELD_ID, RepositoryInterface::CONTENTFUL_ITALIAN_LOCALE_CODE, $googleEventId)
            ->update()
        ;

        if ($isPublished && !$isUpdated &&!$isDraft) {
            $event->publish();
        }
    }

    private function formatLocation(ResourceInterface $contentfulEvent): string
    {
        return $this->translator->trans('app.calendar.location', ['%location%' => $contentfulEvent[EventRepositoryInterface::CONTENTFUL_RESOURCE_PLACE_FIELD_ID]]);
    }
}

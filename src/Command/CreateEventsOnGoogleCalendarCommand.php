<?php

declare(strict_types=1);

namespace App\Command;

use App\Repository\EventRepositoryInterface;
use App\Repository\GoogleEventCalendarRepositoryInterface;
use App\Repository\NewsRepositoryInterface;
use App\Repository\RepositoryInterface;
use App\Service\EventIdResolver;
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

    private EventRepositoryInterface $eventRepository;

    private GoogleEventCalendarRepositoryInterface $googleEventCalendarRepository;

    public function __construct(
        GoogleEventCalendarRepositoryInterface $googleEventCalendarRepository,
        EventRepositoryInterface $eventRepository,
        $name = null
    ) {
        $this->eventRepository = $eventRepository;
        $this->googleEventCalendarRepository = $googleEventCalendarRepository;
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
            $googleEvent = $this->googleEventCalendarRepository->save($event);
            if (!$event[EventRepositoryInterface::CONTENTFUL_RESOURCE_GOOGLE_CALENDAR_ID_FIELD_ID]) {
                $this->eventRepository->addGoogleCalendarId($event, $googleEvent->getId());
            }
        }

        return self::SUCCESS;
    }
}

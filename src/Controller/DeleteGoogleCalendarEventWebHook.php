<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\EventIdResolver;
use App\Service\GoogleCalendarServiceFactory;
use Google_Service_Calendar;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class DeleteGoogleCalendarEventWebHook extends AbstractController
{
    private const CUSTOM_SECRET_HEADER_NAME = 'X-Momo-Secret-Token';

    private string $tokenValue;

    private Google_Service_Calendar $calendarService;

    private EventIdResolver $eventIdResolver;

    private string $calendarId;

    public function __construct(
        string $calendarId,
        string $tokenValue,
        GoogleCalendarServiceFactory $calendarServiceFactory,
        EventIdResolver $eventIdResolver
    ) {
        $this->tokenValue = $tokenValue;
        $this->calendarService = $calendarServiceFactory->create();
        $this->eventIdResolver = $eventIdResolver;
        $this->calendarId = $calendarId;
    }

    public function index(Request $request): Response
    {
        $value = $request->headers->get(self::CUSTOM_SECRET_HEADER_NAME);
        if ($this->tokenValue !== $value) {
            throw $this->createAccessDeniedException();
        }
        $contentfulId = $request->get('contenfulId');
        if (!$contentfulId) {
            return $this->json(['message' => 'No contenful event Id provided'], 400);
        }
        $calendarEventId = $this->eventIdResolver->resolveCalendarId($contentfulId);
        if (!$calendarEventId) {
            return $this->json(
                ['message' => sprintf('No calendar google event Id correspond with contentful event Id %s', $contentfulId)],
                404
            );
        }
        $this->calendarService->events->delete($this->calendarId, $calendarEventId);
        $this->eventIdResolver->deleteAssociation($contentfulId);

        return $this->json(['message' => 'Event deleted successfully from Google Calendar']);
    }
}

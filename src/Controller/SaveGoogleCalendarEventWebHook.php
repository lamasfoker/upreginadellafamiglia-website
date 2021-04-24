<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\EventRepositoryInterface;
use App\Repository\GoogleEventCalendarRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class SaveGoogleCalendarEventWebHook extends AbstractController
{
    private const CUSTOM_SECRET_HEADER_NAME = 'X-Momo-Secret-Token';

    private string $tokenValue;

    private EventRepositoryInterface $eventRepository;

    private GoogleEventCalendarRepositoryInterface $googleEventCalendarRepository;

    public function __construct(
        EventRepositoryInterface $eventRepository,
        string $tokenValue,
        GoogleEventCalendarRepositoryInterface $googleEventCalendarRepository
    ) {
        $this->tokenValue = $tokenValue;
        $this->eventRepository = $eventRepository;
        $this->googleEventCalendarRepository = $googleEventCalendarRepository;
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

        $contentfulEvent = $this->eventRepository->getById($contentfulId);
        $this->googleEventCalendarRepository->save($contentfulEvent);

        return $this->json(['message' => 'Event successfully updated in Google Calendar']);
    }
}

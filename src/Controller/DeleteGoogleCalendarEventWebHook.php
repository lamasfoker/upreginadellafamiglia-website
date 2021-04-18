<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\GoogleEventCalendarRepositoryInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class DeleteGoogleCalendarEventWebHook extends AbstractController
{
    private const CUSTOM_SECRET_HEADER_NAME = 'X-Momo-Secret-Token';

    private string $tokenValue;

    private GoogleEventCalendarRepositoryInterface $googleEventCalendarRepository;

    public function __construct(GoogleEventCalendarRepositoryInterface $googleEventCalendarRepository, string $tokenValue)
    {
        $this->tokenValue = $tokenValue;
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

        try {
            $this->googleEventCalendarRepository->delete($contentfulId);
        } catch (Exception $e) {
            return $this->json(['message' => $e->getMessage()], 404);
        }

        return $this->json(['message' => 'Event deleted successfully from Google Calendar']);
    }
}

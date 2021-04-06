<?php
declare(strict_types=1);

namespace App\Controller;

use JsonException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class Header extends AbstractController
{
    private const HEADER_FILE_LOCATION = __DIR__ . '/../../var/data/links.json';

    public function index(): Response
    {
        $header = $this->extractHeaderData();

        if (!$header) {
            return $this->json('NOT FOUND');
        }

        return $this->render('header.html.twig', ['header' => $header]);
    }

    private function extractHeaderData(): ?array
    {
        $header = file_get_contents(self::HEADER_FILE_LOCATION);
        try {
            $header = json_decode($header, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            return null;
        }

        return is_array($header) ? $header : null;
    }
}

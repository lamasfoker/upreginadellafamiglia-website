<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\BreadcrumbsGetter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class Calendar extends AbstractController
{
    private BreadcrumbsGetter $breadcrumbsGetter;

    public function __construct(BreadcrumbsGetter $breadcrumbsGetter)
    {
        $this->breadcrumbsGetter = $breadcrumbsGetter;
    }

    public function index(): Response
    {
        return $this->render('calendar/index.html.twig', ['breadcrumbs' => $this->breadcrumbsGetter->getCalendarPageBreadcrumbs()]);
    }
}

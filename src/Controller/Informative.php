<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\InformativePageRepositoryInterface;
use App\Service\BreadcrumbsGetter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class Informative extends AbstractController
{
    private BreadcrumbsGetter $breadcrumbsGetter;

    private InformativePageRepositoryInterface $textPageRepository;

    public function __construct(InformativePageRepositoryInterface $textPageRepository, BreadcrumbsGetter $breadcrumbsGetter)
    {
        $this->breadcrumbsGetter = $breadcrumbsGetter;
        $this->textPageRepository = $textPageRepository;
    }

    public function index(string $slug): Response
    {
        $page = $this->textPageRepository->getBySlug($slug);
        if ($page === null) {
            return $this->json('NOT FOUND');
        }
        $breadcrumbs = $this->breadcrumbsGetter->getInformativePageBreadcrumbs(
            $page[InformativePageRepositoryInterface::CONTENTFUL_RESOURCE_TITLE_FIELD_ID],
            $slug
        );

        return $this->render('informative-page/index.html.twig', ['page' => $page, 'breadcrumbs' => $breadcrumbs]);
    }
}

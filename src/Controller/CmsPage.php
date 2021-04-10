<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\CmsPageRepositoryInterface;
use App\Service\BreadcrumbsGetter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class CmsPage extends AbstractController
{
    private CmsPageRepositoryInterface $cmsPageRepository;

    private BreadcrumbsGetter $breadcrumbsGetter;

    public function __construct(CmsPageRepositoryInterface $cmsPageRepository, BreadcrumbsGetter $breadcrumbsGetter)
    {
        $this->cmsPageRepository = $cmsPageRepository;
        $this->breadcrumbsGetter = $breadcrumbsGetter;
    }

    public function index(string $slug): Response
    {
        $page = $this->cmsPageRepository->getBySlug($slug);
        if ($page === null) {
            return $this->json('NOT FOUND');
        }
        $breadcrumbs = $this->breadcrumbsGetter->getCmsPageBreadcrumbs(
            $page[CmsPageRepositoryInterface::CONTENTFUL_RESOURCE_TITLE_FIELD_ID],
            $slug
        );

        return $this->render('cms-page/index.html.twig', ['page' => $page, 'breadcrumbs' => $breadcrumbs]);
    }
}

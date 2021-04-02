<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\CmsPageRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class CmsPage extends AbstractController
{
    private CmsPageRepositoryInterface $cmsPageRepository;

    public function __construct(CmsPageRepositoryInterface $cmsPageRepository)
    {
        $this->cmsPageRepository = $cmsPageRepository;
    }

    public function index(string $slug): Response
    {
        $page = $this->cmsPageRepository->getBySlug($slug);
        if ($page === null) {
            return $this->json('NOT FOUND');
        }
        return $this->render('cms-page/index.html.twig', ['page' => $page]);
    }
}

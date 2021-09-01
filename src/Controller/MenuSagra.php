<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\BreadcrumbsGetter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class MenuSagra extends AbstractController
{
    private BreadcrumbsGetter $breadcrumbsGetter;

    public function __construct(BreadcrumbsGetter $breadcrumbsGetter)
    {
        $this->breadcrumbsGetter = $breadcrumbsGetter;
    }

    public function index(): Response
    {
        $breadcrumbs = $this->breadcrumbsGetter->getMenuSagraPageBreadcrumbs();

        return $this->render('menu-sagra/index.html.twig', ['breadcrumbs' => $breadcrumbs]);
    }
}

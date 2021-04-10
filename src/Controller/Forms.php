<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\FormRepositoryInterface;
use App\Service\BreadcrumbsGetter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class Forms extends AbstractController
{
    private FormRepositoryInterface $formRepository;

    private BreadcrumbsGetter $breadcrumbsGetter;

    public function __construct(FormRepositoryInterface $formRepository, BreadcrumbsGetter $breadcrumbsGetter)
    {
        $this->formRepository = $formRepository;
        $this->breadcrumbsGetter = $breadcrumbsGetter;
    }

    public function list(): Response
    {
        return $this->render(
            'forms/list.html.twig',
            ['forms' => $this->formRepository->getAll(), 'breadcrumbs' => $this->breadcrumbsGetter->getFormsListingBreadcrumbs()]
        );
    }
}

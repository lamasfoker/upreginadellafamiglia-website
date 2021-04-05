<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\FormRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class Forms extends AbstractController
{
    private FormRepositoryInterface $formRepository;

    public function __construct(FormRepositoryInterface $formRepository)
    {
        $this->formRepository = $formRepository;
    }

    public function list(): Response
    {
        return $this->render('forms/list.html.twig', ['forms' => $this->formRepository->getAll()]);
    }
}

<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\FormRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class Forms extends AbstractController
{
    /** @var FormRepository */
    private FormRepository $formRepository;

    public function __construct(FormRepository $formRepository)
    {
        $this->formRepository = $formRepository;
    }

    public function list(): Response
    {
        return $this->render('forms/list.html.twig', ['forms' => $this->formRepository->getAll()]);
    }
}

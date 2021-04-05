<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\NewRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class Homepage extends AbstractController
{
    private NewRepositoryInterface $newRepository;

    public function __construct(NewRepositoryInterface $newRepository)
    {
        $this->newRepository = $newRepository;
    }

    public function index(): Response
    {
        return $this->render('homepage/index.html.twig', ['news' => $this->newRepository->getInHomepageNews()]);
    }
}

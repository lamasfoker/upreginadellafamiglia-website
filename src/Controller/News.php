<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\NewRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class News extends AbstractController
{
    private NewRepositoryInterface $newRepository;

    public function __construct(NewRepositoryInterface $newRepository)
    {
        $this->newRepository = $newRepository;
    }

    public function list(Request $request): Response
    {
        $page = $request->query->getInt('pagina', 1);
        return $this->render('news/list.html.twig', ['news' => $this->newRepository->getAllPaginated($page)]);
    }

    public function index(string $slug): Response
    {
        return $this->render('news/index.html.twig', ['news' => $this->newRepository->getBySlug($slug)]);
    }
}

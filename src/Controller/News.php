<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\NewsRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class News extends AbstractController
{
    // This have to be an even number
    private const NUMBER_OF_NEWS_PER_PAGE = 6;

    private NewsRepositoryInterface $newsRepository;

    public function __construct(NewsRepositoryInterface $newsRepository)
    {
        $this->newsRepository = $newsRepository;
    }

    public function list(Request $request): Response
    {
        $page = $request->query->getInt('pagina', 1);

        return $this->render(
            'news/list.html.twig',
            [
                'news' => $this->newsRepository->getAllPaginated($page, self::NUMBER_OF_NEWS_PER_PAGE),
                'pageCount' => ceil($this->newsRepository->count() / self::NUMBER_OF_NEWS_PER_PAGE)
            ]
        );
    }

    public function index(string $slug): Response
    {
        return $this->render('news/index.html.twig', ['news' => $this->newsRepository->getBySlug($slug)]);
    }
}

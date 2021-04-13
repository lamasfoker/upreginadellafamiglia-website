<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\NewsRepositoryInterface;
use App\Service\BreadcrumbsGetter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class News extends AbstractController
{
    // This have to be an even number
    private const NUMBER_OF_NEWS_PER_PAGE = 6;

    private NewsRepositoryInterface $newsRepository;

    private BreadcrumbsGetter $breadcrumbsGetter;

    public function __construct(NewsRepositoryInterface $newsRepository, BreadcrumbsGetter $breadcrumbsGetter)
    {
        $this->newsRepository = $newsRepository;
        $this->breadcrumbsGetter = $breadcrumbsGetter;
    }

    public function list(Request $request): Response
    {
        $page = $request->query->getInt('pagina', 1);

        return $this->render(
            'news/list.html.twig',
            [
                'news' => $this->newsRepository->getAllPaginated($page, self::NUMBER_OF_NEWS_PER_PAGE),
                'pageCount' => ceil($this->newsRepository->count() / self::NUMBER_OF_NEWS_PER_PAGE),
                'breadcrumbs' => $this->breadcrumbsGetter->getNewsListingBreadcrumbs()
            ]
        );
    }

    public function index(string $slug): Response
    {
        $news = $this->newsRepository->getBySlug($slug);
        if ($news === null) {
            throw $this->createNotFoundException(sprintf('The News with slug %s does not exist', $slug));
        }

        return $this->render(
            'news/index.html.twig',
            [
                'news' => $news,
                'breadcrumbs' => $this->breadcrumbsGetter->getNewsBreadcrumbs($news[NewsRepositoryInterface::CONTENTFUL_RESOURCE_TITLE_FIELD_ID], $slug)
            ]
        );
    }
}

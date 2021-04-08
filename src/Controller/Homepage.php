<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\NewsRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class Homepage extends AbstractController
{
    private const NUMBER_OF_NEWS_IN_THE_SLIDER = 7;

    private NewsRepositoryInterface $newsRepository;

    public function __construct(NewsRepositoryInterface $newsRepository)
    {
        $this->newsRepository = $newsRepository;
    }

    public function index(): Response
    {
        $slider = $this->newsRepository->getAllPaginated(1, self::NUMBER_OF_NEWS_IN_THE_SLIDER);

        return $this->render(
            'homepage/index.html.twig',
            ['news' => $this->newsRepository->getInHomepageNews(), 'slider' => $slider]
        );
    }
}

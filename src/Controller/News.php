<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class News extends AbstractController
{
    public function list(): Response
    {
        return $this->render('news/list.html.twig');
    }

    public function index(): Response
    {
        return $this->render('news/index.html.twig');
    }
}

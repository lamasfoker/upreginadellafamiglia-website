<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class Forms extends AbstractController
{
    public function list(): Response
    {
        return $this->render('forms/list.html.twig');
    }
}

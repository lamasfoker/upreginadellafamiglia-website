<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\CmsPageRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

final class Footer extends AbstractController
{
    private DecoderInterface $decoder;

    public function __construct(DecoderInterface $decoder)
    {
        $this->decoder = $decoder;
    }

    public function index(): Response
    {
        $contents = file_get_contents(CmsPageRepositoryInterface::CMS_PAGE_FILE_LOCATION);
        if (!is_string($contents)) {
            return $this->json('NOT FOUND');
        }

        $json = $this->decoder->decode(
            $contents,
            JsonEncoder::FORMAT,
            [JsonDecode::ASSOCIATIVE => true]
        );

        if (!is_array($json)) {
            return $this->json('NOT FOUND');
        }

        return $this->render('footer.html.twig', ['footer' => $json['footer']]);
    }
}

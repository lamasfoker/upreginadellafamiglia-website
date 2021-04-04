<?php

declare(strict_types=1);

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class ImageExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'contentful_image_render',
                [$this, 'renderContentfulImage']
            )
        ];
    }

    public function renderContentfulImage(string $url, string $width = null, string $height = null): string
    {
        $url .= '?fm=webp&q=80';
        if ($width) {
            $url .= '&w=' . $width;
        }
        if ($height) {
            $url .= '&h=' . $height;
        }
        return $url;
    }
}

<?php

declare(strict_types=1);

namespace App\Twig;

use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class TitleExtension extends AbstractExtension
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'title',
                [$this, 'getTitle']
            )
        ];
    }

    public function getTitle(string $pageName = null): string
    {
        $title = $this->translator->trans('app.homepage.title');
        if ($pageName) {
            $title = $pageName . ' | ' . $title;
        }
        return $title;
    }
}

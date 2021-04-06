<?php
declare(strict_types=1);

namespace App\Twig;

use Symfony\Component\String\Slugger\SluggerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class SlugExtension extends AbstractExtension
{
    private SluggerInterface $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter(
                'slug',
                [$this->slugger, 'slug']
            )
        ];
    }
}

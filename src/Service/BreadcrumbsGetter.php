<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class BreadcrumbsGetter
{
    private UrlGeneratorInterface $urlGenerator;

    private TranslatorInterface $translator;

    public function __construct(UrlGeneratorInterface $urlGenerator, TranslatorInterface $translator)
    {
        $this->urlGenerator = $urlGenerator;
        $this->translator = $translator;
    }

    public function getContactPageBreadcrumbs(): array
    {
        return $this->getFirstLevelPageBreadcrumbs('app.contact_page.breadcrumbs', 'contact_page');
    }

    public function getFormsListingBreadcrumbs(): array
    {
        return $this->getFirstLevelPageBreadcrumbs('app.forms_listing.breadcrumbs', 'forms_listing');
    }

    public function getNewsListingBreadcrumbs(): array
    {
        return $this->getFirstLevelPageBreadcrumbs('app.news_listing.breadcrumbs', 'news_listing');
    }

    public function getCmsPageBreadcrumbs(string $name, string $slug): array
    {
        return $this->getFirstLevelPageBreadcrumbs($name, 'news_listing', ['slug' => $slug]);
    }

    public function getNewsBreadcrumbs(string $name, string $slug): array
    {
        $breadcrumbs = $this->getNewsListingBreadcrumbs();
        $breadcrumbs[] = [
            'name' => $name,
            'href' => $this->urlGenerator->generate(
                'news_index',
                ['slug' => $slug],
                UrlGeneratorInterface::ABSOLUTE_URL
            ),
        ];

        return $breadcrumbs;
    }

    private function getFirstLevelPageBreadcrumbs(string $translation, string $route, array $parameters = []): array
    {
        $breadcrumbs = $this->getHomePageBreadcrumbs();
        $breadcrumbs[] = [
            'name' => $this->translator->trans($translation),
            'href' => $this->urlGenerator->generate(
                $route,
                $parameters,
                UrlGeneratorInterface::ABSOLUTE_URL
            ),
        ];

        return $breadcrumbs;
    }

    private function getHomePageBreadcrumbs(): array
    {
        return [
            [
                'name' => $this->translator->trans('app.homepage.breadcrumbs'),
                'href' => $this->urlGenerator->generate(
                    'homepage',
                    [],
                    UrlGeneratorInterface::ABSOLUTE_URL
                ),
            ],
        ];
    }
}

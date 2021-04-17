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

    /**
     * @return array<array<string>>
     */
    public function getCalendarPageBreadcrumbs(): array
    {
        return $this->getFirstLevelPageBreadcrumbs('app.calendar.breadcrumbs', 'calendar_page');
    }

    /**
     * @return array<array<string>>
     */
    public function getContactPageBreadcrumbs(): array
    {
        return $this->getFirstLevelPageBreadcrumbs('app.contact_page.breadcrumbs', 'contact_page');
    }

    /**
     * @return array<array<string>>
     */
    public function getFormsListingBreadcrumbs(): array
    {
        return $this->getFirstLevelPageBreadcrumbs('app.forms_listing.breadcrumbs', 'forms_listing');
    }

    /**
     * @return array<array<string>>
     */
    public function getNewsListingBreadcrumbs(): array
    {
        return $this->getFirstLevelPageBreadcrumbs('app.news_listing.breadcrumbs', 'news_listing');
    }

    /**
     * @return array<array<string>>
     */
    public function getCmsPageBreadcrumbs(string $name, string $slug): array
    {
        return $this->getFirstLevelPageBreadcrumbs($name, 'news_listing', ['slug' => $slug]);
    }

    /**
     * @return array<array<string>>
     */
    public function getInformativePageBreadcrumbs(string $name, string $slug): array
    {
        return $this->getFirstLevelPageBreadcrumbs($name, 'informative_page', ['slug' => $slug]);
    }

    /**
     * @return array<array<string>>
     */
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

    /**
     * @param array<string> $parameters
     * @return array<array<string>>
     */
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

    /**
     * @return array<array<string>>
     */
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

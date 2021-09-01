<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\BreadcrumbsGetter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class MenuSagra extends AbstractController
{
    private BreadcrumbsGetter $breadcrumbsGetter;

    private const MENU_FOOD = [
        [
            'name' => '5 Tigelle',
            'description' => '',
            'price' => 2,
        ],
        [
            'name' => '5 Pezzi di Gnocco',
            'description' => '',
            'price' => 2.5,
        ],
        [
            'name' => 'Affettato Misto',
            'description' => '100 grammi',
            'price' => 5,
        ],
        [
            'name' => 'Prosciutto Cotto',
            'description' => '100 grammi',
            'price' => 4,
        ],
        [
            'name' => 'Prosciutto Crudo',
            'description' => '100 grammi',
            'price' => 5,
        ],
        [
            'name' => 'Tortelli Verdi',
            'description' => '',
            'price' => 9,
        ],
        [
            'name' => 'Grigliata di Carne con Patatine',
            'description' => 'Composta da scamone, spiedini, praga e salsiccia',
            'price' => 12,
        ],
        [
            'name' => 'Patatine Fritte',
            'description' => '',
            'price' => 2.5,
        ],
        [
            'name' => 'Monoporzione di Stracchino',
            'description' => '',
            'price' => 2,
        ],
        [
            'name' => 'Vaschetta di Nutella',
            'description' => '',
            'price' => 0.5,
        ],
        [
            'name' => 'Battuto di Lardo',
            'description' => '',
            'price' => 0.5,
        ],
    ];

    private const MENU_SWEETS = [
        [
            'name' => 'Zuppa Inglese',
            'description' => '',
            'price' => 3.5,
        ],
        [
            'name' => 'Panna Cotta',
            'description' => 'A scelta tra Frutti di Bosco e Caramello',
            'price' => 3.5,
        ],
        [
            'name' => 'Caffè',
            'description' => 'A scelta tra Espresso, Orzo e Decaffeinato',
            'price' => 1,
        ],
    ];

    private const MENU_BEVERAGES = [
        [
            'name' => 'Acqua Minerale 1L',
            'description' => '',
            'price' => 2,
        ],
        [
            'name' => 'Acqua Minerale ½L',
            'description' => '',
            'price' => 1,
        ],
        [
            'name' => 'Vino Bianco Frizzante',
            'description' => 'Cantina Medici Ermete',
            'price' => 5.5,
        ],
        [
            'name' => 'Lambrusco il Correggio',
            'description' => 'Cantina di Prato',
            'price' => 6,
        ],
        [
            'name' => 'Vino Bianco Spergola Brina D\'Estate',
            'description' => 'Cantina Aljano',
            'price' => 7,
        ],
        [
            'name' => 'Coca Cola in Bottiglia 1L',
            'description' => '',
            'price' => 5,
        ],
        [
            'name' => 'Coca Cola in Lattina 0,33cc',
            'description' => '',
            'price' => 2,
        ],
        [
            'name' => 'Fanta in Lattina 0,33cc',
            'description' => '',
            'price' => 2,
        ],
        [
            'name' => 'Estathé',
            'description' => '',
            'price' => 1,
        ],
        [
            'name' => 'Birra Beck\'s 0,33cc',
            'description' => '',
            'price' => 3,
        ],
        [
            'name' => 'Birra Icnusa 0,66cc',
            'description' => '',
            'price' => 4.5,
        ],
    ];

    public function __construct(BreadcrumbsGetter $breadcrumbsGetter)
    {
        $this->breadcrumbsGetter = $breadcrumbsGetter;
    }

    public function index(): Response
    {
        $breadcrumbs = $this->breadcrumbsGetter->getMenuSagraPageBreadcrumbs();

        return $this->render(
            'menu-sagra/index.html.twig', [
                'breadcrumbs' => $breadcrumbs,
                'menuFood' => self::MENU_FOOD,
                'menuSweets' => self::MENU_SWEETS,
                'menuBeverages' => self::MENU_BEVERAGES,
            ]);
    }
}

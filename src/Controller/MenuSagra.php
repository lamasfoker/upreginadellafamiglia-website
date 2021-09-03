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
            'image' => 'https://images.ctfassets.net/zv2aec8nnyd1/3DleYu8ksBFGQdLyuAZJwX/07bd3624c355e2ae29a016cc737bf875/tigelle.jpg',
        ],
        [
            'name' => '5 Pezzi di Gnocco',
            'description' => '',
            'price' => 2.5,
            'image' => 'https://images.ctfassets.net/zv2aec8nnyd1/2UjVNvlQcvALfeCzxwBNF0/e09bd6b862e77c30a6a7b215c166af39/gnocco.jpg',
        ],
        [
            'name' => 'Affettato Misto',
            'description' => '100 grammi',
            'price' => 5,
            'image' => 'https://images.ctfassets.net/zv2aec8nnyd1/1vOHPRs9C7Npep89dRRFGZ/5181cbe9ebfc181bbb1c73514bc66202/affettato-misto.jpg',
        ],
        [
            'name' => 'Prosciutto Cotto',
            'description' => '100 grammi',
            'price' => 4,
            'image' => 'https://images.ctfassets.net/zv2aec8nnyd1/4MrzkcwCWd1YvQHoMH9IIO/af0207f6315bc352e4862aca72b61cda/prosciutto-cotto.jpg',
        ],
        [
            'name' => 'Prosciutto Crudo',
            'description' => '100 grammi',
            'price' => 5,
            'image' => 'https://images.ctfassets.net/zv2aec8nnyd1/26rLMavxTFn42nblM2UB26/be7217e7ad2cb3bda5204acd8842390e/prosciutto-crudo.jpg',
        ],
        [
            'name' => 'Tortelli Verdi',
            'description' => '',
            'price' => 9,
            'image' => 'https://images.ctfassets.net/zv2aec8nnyd1/6ildKrVni50ZbVNJAW0ZsX/c7c646d5cfe51dee7466b8796705ffa8/tortelli-verdi.jpg',
        ],
        [
            'name' => 'Grigliata di Carne con Patatine',
            'description' => 'Composta da scamone, spiedini, praga e salsiccia',
            'price' => 12,
            'image' => 'https://images.ctfassets.net/zv2aec8nnyd1/1Am8gnEkukwpt503nB1Sm0/d29acc7bb091908beb965900349ba66c/grigliata.jpg',
        ],
        [
            'name' => 'Patatine Fritte',
            'description' => '',
            'price' => 2.5,
            'image' => 'https://images.ctfassets.net/zv2aec8nnyd1/31ezu1yBypnJArqKlTCBR1/257ddc164aa7ecbbfdb2e5cd1fb5bbca/patatine-fritte.jpg',
        ],
        [
            'name' => 'Monoporzione di Stracchino',
            'description' => '',
            'price' => 2,
            'image' => 'https://images.ctfassets.net/zv2aec8nnyd1/4cP7l226bQf528o5CidOuI/aff2c106112c716573c76c2b53829ebb/stracchino.jpg',
        ],
        [
            'name' => 'Vaschetta di Nutella',
            'description' => '',
            'price' => 0.5,
            'image' => 'https://images.ctfassets.net/zv2aec8nnyd1/19vLWr8tigYaiU2ordpyAc/c966eff5afc9221bce340433ee5e0de8/nutella.jpg',
        ],
        [
            'name' => 'Battuto di Lardo',
            'description' => '',
            'price' => 0.5,
            'image' => 'https://images.ctfassets.net/zv2aec8nnyd1/2hWMKRgxb0B7spelPmdF3W/80525f66cd7f250cb0350f1db1345448/lardo.jpg',
        ],
    ];

    private const MENU_SWEETS = [
        [
            'name' => 'Zuppa Inglese',
            'description' => '',
            'price' => 3.5,
            'image' => 'https://images.ctfassets.net/zv2aec8nnyd1/aAyO3E1G4RQVlEIfaRc6C/98e0318fd5744751330c8a93f786f466/zuppa-inglese.jpg',
        ],
        [
            'name' => 'Panna Cotta',
            'description' => 'A scelta tra Frutti di Bosco e Caramello',
            'price' => 3.5,
            'image' => 'https://images.ctfassets.net/zv2aec8nnyd1/2OZCpRrfvcNblzvwz4Gmq3/d53d564adb9e1afe9546085b390bc092/panna-cotta.jpg',
        ],
        [
            'name' => 'Caffè',
            'description' => 'A scelta tra Espresso, Orzo e Decaffeinato',
            'price' => 1,
            'image' => 'https://images.ctfassets.net/zv2aec8nnyd1/2oVxkhTo2bv01DzH5sJhrJ/9026d3e65135970a13c4a9566a899734/caffe.jpg',
        ],
    ];

    private const MENU_BEVERAGES = [
        [
            'name' => 'Acqua Minerale 1L',
            'description' => '',
            'price' => 2,
            'image' => 'https://images.ctfassets.net/zv2aec8nnyd1/3jF8IuoR970Iitgns14HcE/0902447f3385272f4e6359c07d2e065a/acqua.jpg',
        ],
        [
            'name' => 'Acqua Minerale ½L',
            'description' => '',
            'price' => 1,
            'image' => 'https://images.ctfassets.net/zv2aec8nnyd1/6keimqVf6fYAQ2Y7gYaQIn/854a257fa4fe3995fcef30201ab221d6/acqua-mezzo.png',
        ],
        [
            'name' => 'Vino Bianco Frizzante',
            'description' => 'Cantina Medici Ermete',
            'price' => 5.5,
            'image' => 'https://images.ctfassets.net/zv2aec8nnyd1/BkX2m0h6stOao5Q5KEnnO/b1c59cedddff9b9a90a6453c83217f16/Frizzantino-secco.png',
        ],
        [
            'name' => 'Lambrusco il Correggio',
            'description' => 'Cantina di Prato',
            'price' => 6,
            'image' => 'https://images.ctfassets.net/zv2aec8nnyd1/1wHJ3BMX854EIvXvu8dPsa/a67e0258ca39242e2c8638f8bcd17e99/lambrusco.jpg',
        ],
        [
            'name' => 'Vino Bianco Spergola Brina D\'Estate',
            'description' => 'Cantina Aljano',
            'price' => 7,
            'image' => 'https://images.ctfassets.net/zv2aec8nnyd1/2QniYAvr9Wj5TOkRMbUkel/77c615ccc199d8558389b2ee89fb3547/brina-d-estate.jpg',
        ],
        [
            'name' => 'Coca Cola in Bottiglia 1L',
            'description' => '',
            'price' => 5,
            'image' => 'https://images.ctfassets.net/zv2aec8nnyd1/6pqLQBxA93FDsv9YUaPzvG/cd310b3b99b8588abc20bfa2e9bd742e/coca-cola.png',
        ],
        [
            'name' => 'Coca Cola in Lattina 0,33cc',
            'description' => '',
            'price' => 2,
            'image' => 'https://images.ctfassets.net/zv2aec8nnyd1/74Ia70C56SC3j300icO9zr/b1687cc9ba26980508c7ab36a06f924b/coca-cola-lattina.jpeg',
        ],
        [
            'name' => 'Fanta in Lattina 0,33cc',
            'description' => '',
            'price' => 2,
            'image' => 'https://images.ctfassets.net/zv2aec8nnyd1/6d24rh80ztfemWa4g2razd/1fe6db502c926bfe6c7e3b5dbd8a73e3/fanta-lattina.jpg',
        ],
        [
            'name' => 'Estathé',
            'description' => '',
            'price' => 1,
            'image' => 'https://images.ctfassets.net/zv2aec8nnyd1/204XAbaiEWqNpzKY4KFRVK/a27707e60c8c9b6f83b08b30443f4710/estathe.jpg',
        ],
        [
            'name' => 'Birra Beck\'s 0,33cc',
            'description' => '',
            'price' => 3,
            'image' => 'https://images.ctfassets.net/zv2aec8nnyd1/2PCa1ufZZbw3FPpTv5Mgt4/df14ec6747d53beb3d4309d90d2daf7b/birra-beck.jpg',
        ],
        [
            'name' => 'Birra Icnusa 0,66cc',
            'description' => '',
            'price' => 4.5,
            'image' => 'https://images.ctfassets.net/zv2aec8nnyd1/5qUUGX50OLiL4xrfIA2sTS/6d6934a21a27399612a1d3d1b0a54480/birra-ichnusa.jpg',
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
            'menu-sagra/index.html.twig',
            [
                'breadcrumbs' => $breadcrumbs,
                'menuFood' => self::MENU_FOOD,
                'menuSweets' => self::MENU_SWEETS,
                'menuBeverages' => self::MENU_BEVERAGES,
            ]
        );
    }
}

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
            'name' => 'Grigliata di Carne',
            'description' => 'Composta da scamone, spiedini, praga e salsiccia',
            'price' => 11,
            'image' => 'https://images.ctfassets.net/zv2aec8nnyd1/1Am8gnEkukwpt503nB1Sm0/d29acc7bb091908beb965900349ba66c/grigliata.jpg',
        ],
        [
            'name' => 'Patatine Fritte',
            'description' => '',
            'price' => 3,
            'image' => 'https://images.ctfassets.net/zv2aec8nnyd1/19auS85eoO64p8mywVv72x/f1e2231a56719ab076ac76a31c10ebc1/patatine-fritte.jpeg',
        ],
        [
            'name' => 'Pomodori con Cipolla',
            'description' => 'Puoi richiedere la rimozione della cipolla',
            'price' => 2.5,
            'image' => 'https://images.ctfassets.net/zv2aec8nnyd1/A3ZWRr16Vc5vXT3Mydy8Y/d78e42ae8eaf2f52e745bc8dd756f67c/Insalata-di-pomodoro-e-cipolla-calabria-1_cleanup__1_.jpeg',
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
            'name' => 'Bavarese alle Fragole',
            'description' => '',
            'price' => 3.5,
            'image' => 'https://images.ctfassets.net/zv2aec8nnyd1/7m4WdVtfZYgUYB9Tuk3lS6/6b446addb8d7e897d22952c5880b5e16/SH_bavarese_vegan_cleanup.jpeg',
        ],
        [
            'name' => 'Caffè',
            'description' => 'A scelta tra Espresso, Orzo e Ginseng',
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
            'name' => 'Vino Bianco Frizzante 1L',
            'description' => 'Cantina Medici Ermete',
            'price' => 5,
            'image' => 'https://images.ctfassets.net/zv2aec8nnyd1/8K1KhvGHX5Xpe1KMfjhBq/fd89196515b02456c889decf39522eab/wine-decanter-0.5l.jpg',
        ],
        [
            'name' => 'Vino Bianco Frizzante ½L',
            'description' => 'Cantina Medici Ermete',
            'price' => 3.5,
            'image' => 'https://images.ctfassets.net/zv2aec8nnyd1/8K1KhvGHX5Xpe1KMfjhBq/fd89196515b02456c889decf39522eab/wine-decanter-0.5l.jpg',
        ],
        [
            'name' => 'Lambrusco il Correggio',
            'description' => 'Cantina di Prato',
            'price' => 8,
            'image' => 'https://images.ctfassets.net/zv2aec8nnyd1/1wHJ3BMX854EIvXvu8dPsa/a67e0258ca39242e2c8638f8bcd17e99/lambrusco.jpg',
        ],
        [
            'name' => 'Vino Bianco Spergola Brina D\'Estate',
            'description' => 'Cantina Aljano',
            'price' => 10,
            'image' => 'https://images.ctfassets.net/zv2aec8nnyd1/2QniYAvr9Wj5TOkRMbUkel/77c615ccc199d8558389b2ee89fb3547/brina-d-estate.jpg',
        ],
        [
            'name' => 'Coca Cola alla Spina Media 0,40cc',
            'description' => '',
            'price' => 4,
            'image' => 'https://images.ctfassets.net/zv2aec8nnyd1/3DUa1mKDt4YRl8FBNUQBiH/dc0d7751d956737b2f7a9bc52759270c/1200.png.png',
        ],
        [
            'name' => 'Coca Cola alla Spina Piccola 0,25cc',
            'description' => '',
            'price' => 2.5,
            'image' => 'https://images.ctfassets.net/zv2aec8nnyd1/3DUa1mKDt4YRl8FBNUQBiH/dc0d7751d956737b2f7a9bc52759270c/1200.png.png',
        ],
        [
            'name' => 'Birra alla Spina Media 0,40cc',
            'description' => '',
            'price' => 4.5,
            'image' => 'https://images.ctfassets.net/zv2aec8nnyd1/3ut0bH1etbQXAjhxG9lt3z/c2e2b48604cefcd01c487459d9cea789/047_Birra-classica-1000x1000.jpeg',
        ],
        [
            'name' => 'Birra alla Spina Piccola 0,25cc',
            'description' => '',
            'price' => 3,
            'image' => 'https://images.ctfassets.net/zv2aec8nnyd1/3ut0bH1etbQXAjhxG9lt3z/c2e2b48604cefcd01c487459d9cea789/047_Birra-classica-1000x1000.jpeg',
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

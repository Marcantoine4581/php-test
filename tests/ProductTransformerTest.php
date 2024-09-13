<?php

use PHPUnit\Framework\TestCase;

require_once 'ProductTransformer.php';

class ProductTransformerTest extends TestCase
{
    // Instance de ProductTransformer
    private $productTransformer;

    // Configuration avant chaque test
    protected function setUp(): void
    {
        // Crée une instance de la classe ProductTransformer
        $this->productTransformer = new ProductTransformer();
    }

    public function testTransformData()
    {
        // Préparez les données d'entrée simulées
        $object = [
            (object)[
                'id' => 'PCT157135',
                'availability' => 'in stock',
                'product_type' => 'top',
                'general_product' => 'Nare Contour',
                'tops' => [
                    (object)[
                        'availability' => 'in stock',
                        'band' => '34',
                        'cup' => 'C'
                    ],
                    (object)[
                        'availability' => 'in stock',
                        'band' => '32',
                        'cup' => 'B'
                    ],
                    (object)[
                        'availability' => 'out of stock',
                        'band' => '40',
                        'cup' => 'D'
                    ]
                ],
                'bottoms' => [
                    (object)[
                        'availability' => 'in stock',
                        'type' => 'Hipster',
                        'size' => 'XS'
                    ],
                    (object)[
                        'availability' => 'in stock',
                        'type' => 'Brazilian',
                        'size' => 'L'
                    ],
                    (object)[
                        'availability' => 'in stock',
                        'type' => 'G-string',
                        'size' => 'XL'
                    ]
                ]
            ],
            (object)[
                'id' => 'PCT165789',
                'availability' => 'out of stock',
                'product_type' => 'top',
                'general_product' => 'Nare Contour',
                'tops' => [
                    (object)[
                        'availability' => 'in stock',
                        'band' => '34',
                        'cup' => 'C'
                    ],
                    (object)[
                        'availability' => 'in stock',
                        'band' => '32',
                        'cup' => 'B'
                    ],
                    (object)[
                        'availability' => 'in stock',
                        'band' => '40',
                        'cup' => 'DDD'
                    ],
                    (object)[
                        'availability' => 'in stock',
                        'band' => '40',
                        'cup' => 'DD'
                    ],
                    (object)[
                        'availability' => 'in stock',
                        'band' => '38',
                        'cup' => 'I'
                    ]
                ],
                'bottoms' => [
                    (object)[
                        'availability' => 'in stock',
                        'type' => 'Thong',
                        'size' => 'M'
                    ]
                ]
            ]
        ];

        // Exécutez la méthode transformData avec les données simulées
        $result = $this->productTransformer->transformData($object);

        // Résultat attendu
        $expectedResult = [
            "PCT157135" => [
                [
                    'type' => 'top',
                    'name' => 'Nare Contour',
                    'fields' => [
                        [
                            'label' => 'band size',
                            'values' => ['32', '34', '38', '40']
                        ],
                        [
                            'label' => 'cup size',
                            'values' => ['B', 'C', 'DD', 'DDD', 'I']
                        ]
                    ],
                    'possible_values' => [
                        ['band size' => '32', 'cup size' => 'B'],
                        ['band size' => '34', 'cup size' => 'C'],
                        ['band size' => '38', 'cup size' => 'I'],
                        ['band size' => '40', 'cup size' => 'DD'],
                        ['band size' => '40', 'cup size' => 'DDD']
                    ]
                ],
                [
                    'type' => 'top',
                    'name' => 'Nare Contour',
                    'fields' => [
                        [
                            'label' => 'panty shape',
                            'values' => [
                                [
                                    'image' => '/assets/images/panties/collection.svg#Brazilian',
                                    'value' => 'Brazilian'
                                ],
                                [
                                    'image' => '/assets/images/panties/collection.svg#G-string',
                                    'value' => 'G-string'
                                ],
                                [
                                    'image' => '/assets/images/panties/collection.svg#Hipster',
                                    'value' => 'Hipster'
                                ],
                                [
                                    'image' => '/assets/images/panties/collection.svg#Thong',
                                    'value' => 'Thong'
                                ]
                            ]
                        ]
                    ],
                    'possible_values' => [
                        ['panty shape' => 'Brazilian', 'panty size' => 'L'],
                        ['panty shape' => 'G-string', 'panty size' => 'XL'],
                        ['panty shape' => 'Hipster', 'panty size' => 'XS'],
                        ['panty shape' => 'Thong', 'panty size' => 'M']
                    ]
                ]
            ],
            "PCT165789" => [
                [
                    'type' => 'top',
                    'name' => 'Nare Contour',
                    'fields' => [
                        [
                            'label' => 'band size',
                            'values' => ['32', '34', '38', '40']
                        ],
                        [
                            'label' => 'cup size',
                            'values' => ['B', 'C', 'DD', 'DDD', 'I']
                        ]
                    ],
                    'possible_values' => [
                        ['band size' => '32', 'cup size' => 'B'],
                        ['band size' => '34', 'cup size' => 'C'],
                        ['band size' => '38', 'cup size' => 'I'],
                        ['band size' => '40', 'cup size' => 'DD'],
                        ['band size' => '40', 'cup size' => 'DDD']
                    ]
                ],
                [
                    'type' => 'top',
                    'name' => 'Nare Contour',
                    'fields' => [
                        [
                            'label' => 'panty shape',
                            'values' => [
                                [
                                    'image' => '/assets/images/panties/collection.svg#Brazilian',
                                    'value' => 'Brazilian'
                                ],
                                [
                                    'image' => '/assets/images/panties/collection.svg#G-string',
                                    'value' => 'G-string'
                                ],
                                [
                                    'image' => '/assets/images/panties/collection.svg#Hipster',
                                    'value' => 'Hipster'
                                ],
                                [
                                    'image' => '/assets/images/panties/collection.svg#Thong',
                                    'value' => 'Thong'
                                ]
                            ]
                        ]
                    ],
                    'possible_values' => [
                        ['panty shape' => 'Brazilian', 'panty size' => 'L'],
                        ['panty shape' => 'G-string', 'panty size' => 'XL'],
                        ['panty shape' => 'Hipster', 'panty size' => 'XS'],
                        ['panty shape' => 'Thong', 'panty size' => 'M']
                    ]
                ]
            ]
        ];

        // Vérifiez que le résultat est identique au résultat attendu
        $this->assertEquals($expectedResult, $result);
    }

    public function testTransformDataWithEmptyData()
    {
        // Cas de test avec des données vides
        $object = [];

        $result = $this->productTransformer->transformData($object);

        $this->assertEquals([], $result);
    }
}

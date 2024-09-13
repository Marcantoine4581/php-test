<?php

use PHPUnit\Framework\TestCase;

class FonctionsTest extends TestCase
{
    // Test pour la fonction saveToJsonFile
    public function testsaveToJsonFile()
    {
        require_once 'functions.php';

        $transformedData = [
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
        ];

        $expectedResult = '{
    "PCT157135": [
        {
            "type": "top",
            "name": "Nare Contour",
            "fields": [
                {
                    "label": "band size",
                    "values": [
                        "32",
                        "34",
                        "38",
                        "40"
                    ]
                },
                {
                    "label": "cup size",
                    "values": [
                        "B",
                        "C",
                        "DD",
                        "DDD",
                        "I"
                    ]
                }
            ],
            "possible_values": [
                {
                    "band size": "32",
                    "cup size": "B"
                },
                {
                    "band size": "34",
                    "cup size": "C"
                },
                {
                    "band size": "38",
                    "cup size": "I"
                },
                {
                    "band size": "40",
                    "cup size": "DD"
                },
                {
                    "band size": "40",
                    "cup size": "DDD"
                }
            ]
        },
        {
            "type": "top",
            "name": "Nare Contour",
            "fields": [
                {
                    "label": "panty shape",
                    "values": [
                        {
                            "image": "\/assets\/images\/panties\/collection.svg#Brazilian",
                            "value": "Brazilian"
                        },
                        {
                            "image": "\/assets\/images\/panties\/collection.svg#G-string",
                            "value": "G-string"
                        },
                        {
                            "image": "\/assets\/images\/panties\/collection.svg#Hipster",
                            "value": "Hipster"
                        },
                        {
                            "image": "\/assets\/images\/panties\/collection.svg#Thong",
                            "value": "Thong"
                        }
                    ]
                }
            ],
            "possible_values": [
                {
                    "panty shape": "Brazilian",
                    "panty size": "L"
                },
                {
                    "panty shape": "G-string",
                    "panty size": "XL"
                },
                {
                    "panty shape": "Hipster",
                    "panty size": "XS"
                },
                {
                    "panty shape": "Thong",
                    "panty size": "M"
                }
            ]
        }
    ]
}';

        $fileName = "test.json";
        saveToJsonFile($transformedData, $fileName);
        $result = file_get_contents('./test.json');
        $this->assertEquals($expectedResult, $result);
    }
}

<?php

class ProductTransformer
{
    public function transformData(array $products): array
    {
        $result = [];
        foreach ($products as $value) {
            // Vérification des produits disponibles.
            if (
                $value->availability === "in stock"
                || array_map(function (object $top) {
                    if ($top->availability === "in stock") {
                        return true;
                    }
                }, $value->tops)
                || array_map(function (object $bottom) {
                    if ($bottom->availability === "in stock") {
                        return true;
                    }
                }, $value->bottoms)
            ) {
                // Préparation des données pour les tops.
                $topsData = [
                    "type" => $value->product_type,
                    "name" => $value->general_product,
                    "fields" => [
                        [
                            "label" => "band size",
                            "values" => $this->getAndSortBandValues($value->tops)
                        ],
                        [
                            "label" => "cup size",
                            "values" => $this->getAndSortCupValues($value->tops)
                        ]
                    ],
                    "possible_values" => (is_array($value->tops)) ?
                        $this->cleanAndFilterArray(array_map(function (object $top) {
                            if ($top->availability === "in stock" && ($top->band !== "" || $top->cup !== "")) {
                                return [
                                    "band size" => $top->band,
                                    "cup size" => $top->cup
                                ];
                            }
                        }, $value->tops))
                        : []
                ];

                // Préparation des données pour les bottoms.
                $bottomsData = [
                    "type" => $value->product_type,
                    "name" => $value->general_product,
                    "fields" => [
                        [
                            "label" => "panty shape",
                            "values" => (is_array($value->bottoms)) ?
                                array_values(array_unique(array_filter(array_map(function (object $bandValue) {
                                    if ($bandValue->availability === "in stock") {
                                        return ($bandValue->type !== "") ? [
                                            "image" => "/assets/images/panties/collection.svg#$bandValue->type",
                                            "value" => $bandValue->type
                                        ] : [
                                            "image" => "",
                                            "value" => $bandValue->type
                                        ];
                                    }
                                }, $value->bottoms)), SORT_REGULAR))
                                : []
                        ],
                    ],
                    "possible_values" => (is_array($value->bottoms)) ?
                        $this->cleanAndFilterArray(array_map(function (object $bottom) {
                            if ($bottom->availability === "in stock" && ($bottom->type !== "" || $bottom->size !== "")) {
                                return [
                                    "panty shape" => $bottom->type,
                                    "panty size" => $bottom->size
                                ];
                            }
                        }, $value->bottoms))
                        : []
                ];

                // Création de formdata avec tops et bottoms
                $formdata = [$topsData, $bottomsData];

                // Récupération du nom du produit actuellement traité.
                $name = $value->general_product;

                // Fusion des valeurs des tailles présentes dans "fields" pour les tops et bottoms.
                $this->mergeValuesFromFields($result, $name, $formdata);

                // Fusion des "possible-values" pour les Tops
                $this->mergeTopsPossibleValues($result, $name, $formdata[0]);


                // Fusion des "possible-values" pour les Bottoms
                $this->mergeBottomsPossibleValues($result, $name, $formdata[1]);

                // Copie le pouveau produit dans le tableau résult.
                $result[$value->id] = $formdata;
            }
        }
        return $result;
    }

    // Récupère les valeurs de Cup pour les produits disponibles.
    private function getAndSortCupValues(array &$values)
    {
        $newArray = array_values(array_unique(array_filter(array_map(function (object $cupValue) {
            return $cupValue->availability === "in stock" ? $cupValue->cup : [];
        }, $values))));
        sort($newArray);
        return $newArray;
    }

    // Récupère les valeurs de Bands pour les produits disponibles.
    private function getAndSortBandValues(array &$values)
    {
        $newArray = array_values(array_unique(array_filter(array_map(function (object $bandValue) {
            return $bandValue->availability === "in stock" ? $bandValue->band : [];
        }, $values))));
        sort($newArray);
        return $newArray;
    }

    // Fonction utilisé uniquement dans cleanAndFilterArray()
    private function myFilter($var)
    {
        return ($var !== NULL && $var !== "");
    }

    // Supprime tous les doublons, Supprime les entrées vides d'un tableau, et retourne les veleurs d'un tableau.
    private function cleanAndFilterArray($array): array
    {
        $sortedArrayWithoutDuplication = array_unique($array, SORT_REGULAR);
        $arrayWithoutNullValues = array_filter($sortedArrayWithoutDuplication, [$this, "myFilter"]);
        $cleanArray = array_values($arrayWithoutNullValues);
        return $cleanArray;
    }

    // Fusion et tri des valeurs des tailles présentes dans "fields" pour les tops et bottoms.
    private function mergeValuesFromFields(&$result, $name, &$formdata)
    {
        foreach ($result as $key => &$items) {
            foreach ($items as &$item) {
                if ($item['name'] === $name) {
                    foreach ($item['fields'] as &$existingField) {
                        foreach ($formdata as &$bodypart) {
                            foreach ($bodypart['fields'] as &$newField) {
                                // Compare les labels pour fusionner les valeurs
                                if ($existingField['label'] === $newField['label']) {
                                    $mergedValues = array_values(array_unique(array_merge($existingField['values'], $newField['values']), SORT_REGULAR));
                                    sort($mergedValues);
                                    $existingField['values'] = $mergedValues; // Copie les valeurs fusionnées dans "result"
                                    $newField['values'] = $mergedValues; // // Copie les valeurs fusionnées dans "formdata"
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    // Fusion des "possible-values" pour les Tops.
    private function mergeTopsPossibleValues(&$result, $name, &$topsData)
    {
        foreach ($result as &$items) {
            if ($items[0]['name'] === $name) {
                $mergedTopPossibleValues = array_values(array_unique(array_merge($items[0]['possible_values'], $topsData['possible_values']), SORT_REGULAR));
                $mergedTopPossibleValuesWithoutDuplicate = $this->removeDuplicates($mergedTopPossibleValues);
                $items[0]['possible_values'] = $this->sortTopsPossibleValues($mergedTopPossibleValuesWithoutDuplicate);
                $topsData['possible_values'] = $this->sortTopsPossibleValues($mergedTopPossibleValuesWithoutDuplicate);
            }
        }
    }

    // Fusion des "possible-values" pour les Bottoms.
    private function mergeBottomsPossibleValues(&$result, $name, &$bottomsData)
    {
        foreach ($result as &$items) {
            if ($items[1]['name'] === $name) {
                $mergedBottomsPossibleValues = array_values(array_unique(array_merge($items[1]['possible_values'], $bottomsData['possible_values']), SORT_REGULAR));
                $mergedBottomsPossibleValuesWithoutDuplicate = $this->removeDuplicates($mergedBottomsPossibleValues);
                $items[1]['possible_values'] = $this->sortBottomsPossibleValues($mergedBottomsPossibleValuesWithoutDuplicate);
                $bottomsData['possible_values'] = $this->sortBottomsPossibleValues($mergedBottomsPossibleValuesWithoutDuplicate);
            }
        }
    }

    // Fonction de tri des Possible Values pour les Tops.
    private function sortTopsPossibleValues(?array $tops): array
    {
        // Vérifier que l'argument est bien un tableau et n'est pas vide
        if (!is_array($tops) || empty($tops)) {
            return [];
        }
        usort($tops, function ($a, $b) {
            // Compare d'abord "band size"
            $shapeComparison = strcmp($a['band size'], $b['band size']);
            if ($shapeComparison === 0) {
                // Si "band size" est identique, compare "cup size"
                return strcmp($a['cup size'], $b['cup size']);
            }
            return $shapeComparison;
        });

        return $tops;
    }

    // Fonction de tri des Possible Values pour les Bottoms.
    private function sortBottomsPossibleValues(?array $bottoms): array
    {
        // Vérifier que l'argument est bien un tableau et n'est pas vide
        if (!is_array($bottoms) || empty($bottoms)) {
            return [];
        }
        usort($bottoms, function ($a, $b) {
            // Compare d'abord "panty shape"
            $shapeComparison = strcmp($a['panty shape'], $b['panty shape']);
            if ($shapeComparison === 0) {
                // Si "panty shape" est identique, compare "panty size"
                return strcmp($a['panty size'], $b['panty size']);
            }
            return $shapeComparison;
        });

        return $bottoms;
    }

    // Supprime les doublons pour les "possible values".
    private function removeDuplicates(array $array): array
    {
        // Utilise serialize pour traiter les tableaux multidimensionnels comme des chaînes uniques
        $serializedArray = array_map('serialize', $array);

        // Supprime les doublons dans le tableau sérialisé
        $uniqueSerializedArray = array_unique($serializedArray);

        // Désérialise le tableau pour revenir aux données originales
        $uniqueArray = array_map('unserialize', $uniqueSerializedArray);

        return $uniqueArray;
    }
}

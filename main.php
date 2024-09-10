<?php

// URL du flux de données
$url = "https://gw-services.prd.adoreme.com/v2/feeds/veesual";


// Fonction pour télécharger les données depuis l'URL
function downloadData($url)
{
    $ch = curl_init();

    // Paramétrer l'URL
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Télécharger les données
    $data = curl_exec($ch);

    // Gérer les erreurs
    if (curl_errno($ch)) {
        echo 'Erreur Curl : ' . curl_error($ch);
        return false;
    }

    // Fermer la session cURL
    curl_close($ch);

    return $data;
}

// Fonction pour sauvegarder les données au format JSON
function saveToJsonFile($data, $filename)
{
    // Conversion du tableau PHP en JSON
    $jsonData = json_encode($data, JSON_PRETTY_PRINT);

    // Sauvegarder dans un fichier
    if (file_put_contents($filename, $jsonData)) {
        echo "Les données ont été sauvegardées avec succès dans $filename\n";
    } else {
        echo "Erreur lors de la sauvegarde des données\n";
    }
}

function myFilter($var)
{
    return ($var !== NULL && $var !== "");
}

function deleteKeysInArray($array)
{
    $result = array_unique($array, SORT_REGULAR);
    $resultTer = array_filter($result, "myFilter");
    $resultBis = array_values($resultTer);
    return $resultBis;
}

function isArray($array)
{
    return ($array !== NULL && $array !== "" && count($array) > 0);
}

// Transformation en une structure de type groupée
function transformData($object)
{
    $result = [];


    foreach ($object as $value) {
        if (
            $value->availability === "in stock"
            || array_map(function ($top) {
                if ($top->availability === "in stock") {
                    return true;
                }
            }, $value->tops)
            || array_map(function ($bottom) {
                if ($bottom->availability === "in stock") {
                    return true;
                }
            }, $value->bottoms)
        ) {
            $formdata = [
                [
                    "type" => $value->product_type,
                    "name" => $value->general_product,
                    "fields" => [
                        [
                            "label" => "band size",
                            "values" => deleteKeysInArray(array_map(function ($bandValue) {
                                if ($bandValue->availability === "in stock") {
                                    return $bandValue->band;
                                }
                            }, $value->tops)) // extrait les tailles de band
                        ],
                        [
                            "label" => "cup size",
                            "values" => deleteKeysInArray(array_map(function ($cupValue) {
                                return $cupValue->cup;
                            }, $value->tops)) // extrait les tailles de band
                        ]
                    ],
                    "possible_values" => (is_array($value->tops)) ?
                        array_filter(array_map(function ($top) {
                            if ($top->availability === "in stock" && ($top->band !== "" || $top->cup !== "")) {
                                return [
                                    "band size" => $top->band,
                                    "cup size" => $top->cup
                                ];
                            }
                        }, $value->tops))
                        : []  // extrait les valeurs possibles
                ],
                [
                    "type" => $value->product_type,
                    "name" => $value->general_product,
                    "fields" => [
                        [
                            "label" => "panty shape",
                            "values" => (is_array($value->bottoms)) ?
                                deleteKeysInArray(array_map(function ($bandValue) {
                                    if ($bandValue->availability === "in stock") {
                                        return $bandValue->type;
                                    }
                                }, $value->bottoms))
                                : [] // extrait les tailles de band
                        ],
                    ],
                    "possible_values" => (is_array($value->bottoms)) ?
                        array_filter(array_map(function ($bottom) {
                            if ($bottom->availability === "in stock" && ($bottom->type !== "" || $bottom->size !== "")) {
                                return [
                                    "panty shape" => $bottom->type,
                                    "panty size" => $bottom->size
                                ];
                            }
                        }, $value->bottoms))
                        : [] // extrait les valeurs possibles
                ]
            ];
            // var_dump($formdata);
            $result[$value->id] = $formdata;
        } else {
            continue;
        }
    }
    return $result;
}

// Exécution du script
$data = downloadData($url);
$obj = json_decode($data);
$result = transformData($obj);

// var_dump($result);
if ($result !== false) {
    saveToJsonFile($result, 'donnees_transformees.json');
} else {
    echo "Erreur lors du téléchargement des données.\n";
}

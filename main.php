<?php
include 'functions.php';
require_once 'ProductTransformer.php';

/* Exécution du script */

// URL du flux de données
$url = "https://gw-services.prd.adoreme.com/v2/feeds/veesual";

// Téchargement des données depuis l'URL
$data = downloadData($url);

// Récupère une chaîne encodée JSON et la convertit en une valeur de PHP.
$products = json_decode($data);

// Création de l'instance de ProductTransformer
$productTransformer = new ProductTransformer();

// Appel de la méthode transformData avec les données des produits
$transformedData = $productTransformer->transformData($products);

// Sauvegarde les données dans un fichier au format JSON (transformed_data.json)
saveToJsonFile($transformedData, 'transformed_data.json');

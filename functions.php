<?php

/* Fonction pour télécharger les données depuis l'URL */
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

/* Fonction pour sauvegarder les données au format JSON */
function saveToJsonFile(array $data, $filename): void
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

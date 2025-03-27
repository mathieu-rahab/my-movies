<?php

require_once '../class/mymovies/Director.php'; // !! Changer le chemin !!

function searchDirector($userInput): array
{
    $director = new Director();
    if (is_numeric($userInput)) {
        // Recherche par ID
        $results = $director->getDirectorById($userInput);
    } else {
        $results = $director->searchByName($userInput);
    }

    if (!empty($results)) {
        return $results;
    } else {
        return ['error' => 'Aucun réalisateur trouvé correspondant à votre recherche.'];
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['searchQuery'])) {
    $searchQuery = $_GET['searchQuery'];
    $results = searchDirector($searchQuery);
    echo json_encode($results);
}

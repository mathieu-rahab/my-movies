<?php
require_once '../class/mymovies/Actor.php'; // !! Changer le chemin !!

function searchActor($userInput): array
{
    $actor = new Actor();
    if (is_numeric($userInput)) {
        // Recherche par ID
        $results = $actor->findById($userInput);
    } else {
        $results = $actor->searchByName($userInput);
    }

    if (!empty($results)) {
        return $results;
    } else {
        return ['error' => 'Aucun acteur trouvé correspondant à votre recherche.'];
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['searchQuery'])) {
    $searchQuery = $_GET['searchQuery'];
    $results = searchActor($searchQuery);
    echo json_encode($results);
}

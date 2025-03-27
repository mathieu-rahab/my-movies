<?php
require_once '../class/mymovies/Movie.php'; // !! Changer le chemin !!
/**
 *  Vérifie si une chaîne de caractères est un nombre entier valide.
 * @return bool Retourne true si l'input est un nombre entier, sinon false.
 */
function isNumeric($input): bool
{
    return filter_var($input, FILTER_VALIDATE_INT) !== false;
}

function searchFilm($userInput): array
{
    $movie = new Movie();
    if (is_numeric($userInput)) {
        // Recherche par ID
        $results = $movie->findById($userInput);
    } else {
        // Recherche par titre avec une correspondance partielle
        $results = $movie->searchByTitle($userInput);
    }

    if (!empty($results)) {
        return $results;
    } else {
        return ['error' => 'Aucun film trouvé correspondant à votre recherche.'];
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['searchQuery'])) {
    $searchQuery = $_GET['searchQuery'];
    $results = searchFilm($searchQuery);
    echo json_encode($results);
}


<?php
require_once '../class/mymovies/Director.php';
require_once '../class/mymovies/Actor.php';
require_once '../class/mymovies/Movie.php';
require_once '../class/mymovies/Tag.php';
require_once '../class/mymovies/Database.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['type'])) {
    $db = Database::getInstance()->getPDO();
    $type = $_GET['type'];

    $conditions = [];
    $params = [];
    $entity = '';

    if ($type === 'movie') {
        $entity = 'Movie';
        if (!empty($_GET['title'])) {
            $conditions[] = "title LIKE :title";
            $params['title'] = '%' . $_GET['title'] . '%';
        }

        //Recherche par année
        if (!empty($_GET['year']) AND !empty($_GET['year_logic']) AND !empty($_GET['year_operator'])) {
            $valid_operators = ['=', '<', '>'];
            $operator = in_array($_GET['year_operator'], $valid_operators) ? $_GET['year_operator'] : '=';
            $conditions[] = ($_GET['year_logic'] === 'AND' ? 'AND ' : 'OR ') . "YEAR(release_date) $operator :year";
            $params['year'] = $_GET['year'];
        }

        //Recherche par tag
        if (!empty($_GET['tag']) AND !empty($_GET['tag_logic'])) {
            $conditions[] = ($_GET['tag_logic'] === 'AND' ? 'AND ' : 'OR ') . "EXISTS (SELECT 1 FROM TagOfMovie tm JOIN Tag t ON tm.id_tag = t.id_tag WHERE tm.id_movie = Movie.id_movie AND t.name LIKE :tag)";
            $params['screen'] = '%' . $_GET['tag'] . '%';
        }
    } elseif ($type === 'actor' || $type === 'director') {
        $entity = $type === 'actor' ? 'Actor' : 'Director';
        if (!empty($_GET['name'])) {
            $conditions[] = "(last_name LIKE :name OR first_name LIKE :name)";
            $params['name'] = '%' . $_GET['name'] . '%';
        }
        //On utilisera soit name, soit l'id (name doit être vide)
        if (!empty($_GET['id']) && empty($_GET['name'])) { 
            $conditions[] = "id_" . $type . " = :id";
            $params['id'] = $_GET['id'];
        }
        //Recherche par tag
        if (!empty($_GET['tag']) AND !empty($_GET['tag_logic'])) {

            $conditions[] = ($_GET['tag_logic'] === 'AND' ? 'AND ' : 'OR ') ."EXISTS (
            SELECT 1 FROM Movie m
            JOIN TagOfMovie tm ON m.id_movie = tm.id_movie
            JOIN Tag t ON tm.id_tag = t.id_tag
            WHERE m.id_director = Director.id_director
            AND t.name LIKE :tag)";
            $params['tag'] = '%' . $_GET['tag'] . '%';
        }
        //Recherche par année
        if (!empty($_GET['year'] && !empty($_GET['year_operator'])) AND !empty($_GET['year_logic'])) {
            $valid_operators = ['=', '<', '>'];
            $operator = in_array($_GET['year_operator'], $valid_operators) ? $_GET['year_operator'] : '=';
            $subquery = $type === 'actor' ? "JOIN ActIn ai ON ai.id_actor = {$entity}.id_{$type}" : "JOIN Movie m ON m.id_director = {$entity}.id_{$type}";
            $conditions[] = ($_GET['year_logic'] === 'AND' ? 'AND ' : 'OR ')."EXISTS (SELECT 1 FROM Movie {$subquery} WHERE YEAR(release_date) {$operator} :year)";
            $params['year'] = $_GET['year'];
        }
    }


    if (!empty($conditions)) {
        $sql = "SELECT * FROM {$entity} WHERE " . implode('',$conditions);
        $stmt = $db->query($sql, $params);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($results);
    } else {
        echo json_encode(['error' => 'Critères de recherche invalides.']);
    }
}


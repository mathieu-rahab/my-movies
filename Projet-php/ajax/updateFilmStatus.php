
<?php
//Requête qui permet de modifier si un film a été vu ou non, et ça note
session_start();
require '../class/mymovies/Auth.php';

// Vérifier si l'utilisateur est connecté
$auth = new Auth();
$is_logged_in = $auth->isLoggedIn();

if (!$is_logged_in) {
    echo json_encode(['success' => false, 'message' => 'Vous devez être connecté pour effectuer cette action']);
    exit;
}

// Inclure la configuration de la base de données
require_once '../class/mymovies/Database.php'; //Changer le chemin de la classe
$pdo = Database::getInstance();

// Récupérer les données POST
$id = $_POST['id'] ?? null;
$viewed = isset($_POST['viewed']) ? (int)$_POST['viewed'] : 0;
$rating = $_POST['rating'] ?? 0;

// Validation de la note
if ($rating < 0 || $rating > 5) {
    echo json_encode(['success' => false, 'message' => 'La note doit être comprise entre 0 et 5.']);
    exit;
}

// Mettre à jour le film dans la base de données
$sql = "UPDATE Movie SET viewed = ?, rating = ? WHERE id_movie = ?";
$statement = $pdo->query($sql, [$viewed, $rating, $id]);
$result = $statement->fetch(PDO::FETCH_ASSOC);


if (!$result) {
    echo json_encode(['success' => true, 'message' => 'Film mis à jour avec succès.' ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour du film.']);
}
?>

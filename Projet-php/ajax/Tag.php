<?php
require_once '../class/mymovies/Tag.php';

session_start();
require '../class/mymovies/Auth.php';
require '../class/mymovies/Database.php';

// Vérifier si l'utilisateur est connecté
$auth = new Auth();
$is_logged_in = $auth->isLoggedIn();

if (!$is_logged_in) {
    echo json_encode(['success' => false, 'message' => 'Vous devez être connecté pour effectuer cette action']);
    exit;
}






if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    $tag = new Tag();
    $response = [];

    switch ($action) {
        case 'add'://permet de rajouter un tag
            if (isset($_POST['name'])) {
                $response = $tag->add(htmlspecialchars($_POST['name']));
            } else {
                $response = ['error' => true, 'message' => 'Nom du tag manquant'];
            }
            break;
        
        case 'update'://permet de modifier un tag
            if (isset($_POST['id'], $_POST['name'])) {
                $response = $tag->update(htmlspecialchars($_POST['id']), htmlspecialchars($_POST['name']));
            } else {
                $response = ['error' => true, 'message' => 'ID ou nom du tag manquant'];
            }
            break;

        case 'delete':
            if (isset($_POST['id'])) {
                $response = $tag->delete(htmlspecialchars($_POST['id']));
            } else {
                $response = ['error' => true, 'message' => 'ID du tag manquant'];
            }
            break;

        default:
            $response = ['error' => true, 'message' => 'Action non valide' . $action];
    }

    echo json_encode($response);
}
?>

<?php
require_once '../class/mymovies/Actor.php';


session_start();
require '../class/mymovies/Auth.php';

// Vérifier si l'utilisateur est connecté
$auth = new Auth();
$is_logged_in = $auth->isLoggedIn();

if (!$is_logged_in) {
    echo json_encode(['success' => false, 'message' => 'Vous devez être connecté pour effectuer cette action']);
    exit;
}




if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    $actor = new Actor();
    $response = [];

    switch ($action) {
        case 'add':
            if (isset($_POST['last_name'], $_POST['first_name'], $_FILES['photo'])) {
                // Générer un identifiant aléatoire de 5 chiffres
                $random_id = rand(10000, 99999);
    
                // Extraire l'extension du fichier original
                $file_extension = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
                $new_filename = $random_id . '.' . $file_extension;// Créer le nouveau nom de fichier avec l'extension
    
                // Ajout du réalisateur avec le nouveau nom de fichier
                $response = $actor->add(htmlspecialchars($_POST['last_name']), htmlspecialchars($_POST['first_name']), $new_filename);
    
                // Vérifie si l'ajout a été réussi sans erreur
                if (isset($response['success'])) {
                    // Spécifie le chemin de destination
                    $target_directory = "../data/people/";
                    $target_file = $target_directory . $new_filename;
    
                    // Tentative de déplacement du fichier uploadé vers le dossier de destination
                    if (!move_uploaded_file($_FILES['photo']['tmp_name'], $target_file)) {
                         $response = ['error' => true, 'message' => "Erreur lors de l'upload du fichier."];
                    }
            
                }
            }
            break;
                
        case 'update':
            if (isset($_POST['id'], $_POST['last_name'], $_POST['first_name'], $_POST['photo'])) {
                $response = $actor->update(htmlspecialchars($_POST['id']), htmlspecialchars($_POST['last_name']), htmlspecialchars($_POST['first_name']), htmlspecialchars($_POST['photo']));
            } else {
                $response = ['error' => true, 'message' => 'Informations du réalisateur manquantes'];
            }
            break;

        case 'delete':
            if (isset($_POST['id'])) {
                $response = $actor->delete(htmlspecialchars($_POST['id']));
            } else {
                $response = ['error' => true, 'message' => 'ID du réalisateur manquant'];
            }
            break;

        default:
            $response = ['error' => true, 'message' => 'Action non valide'];
    }

    echo json_encode($response);
}
?>

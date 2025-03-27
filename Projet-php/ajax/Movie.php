<?php
require_once '../class/mymovies/Movie.php';
require_once '../class/mymovies/Tag.php';

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
    $film = new Movie();
    $response = [];

    switch ($action) {
        case 'add':
            if (isset($_POST['title'], $_POST['releaseDate'], $_POST['synopsis'], $_POST['directorId'], $_POST['seen'], $_POST['rating'], $_POST['actors'], $_POST['tags'], $_FILES['poster'])) {
                // Générer un identifiant aléatoire de 5 chiffres pour le nom du fichier
                $random_id = rand(10000, 99999);

                // Extraire l'extension du fichier original
                $file_extension = pathinfo($_FILES['poster']['name'], PATHINFO_EXTENSION);
                $new_filename = $random_id . '.' . $file_extension; // Créer le nouveau nom de fichier avec l'extension

                // Ajout du film avec le nouveau nom de fichier
                $response = $film->add(
                    htmlspecialchars($_POST['title']),
                    htmlspecialchars($_POST['releaseDate']),
                    htmlspecialchars($new_filename),
                    htmlspecialchars($_POST['synopsis']),
                    htmlspecialchars($_POST['directorId']),
                    htmlspecialchars($_POST['seen']),
                    htmlspecialchars($_POST['rating']),
                    htmlspecialchars($_POST['tags']),
                    htmlspecialchars($_POST['actors'])
                );


                // Vérifie si l'ajout a été réussi sans erreur
                if (isset($response['success'])) {
                    // Spécifie le chemin de destination
                    $target_directory = "../data/poster/";
                    $target_file = $target_directory . $new_filename;

                    // Tentative de déplacement du fichier uploadé vers le dossier de destination
                    if (!move_uploaded_file($_FILES['poster']['tmp_name'], $target_file)) {
                        $response = ['error' => true, 'message' => "Erreur lors de l'upload du fichier."];
                    }

                }

            } else {
                $response = ['error' => true, 'message' => 'Informations du film manquantes', 'received' => $_REQUEST];
            }
            break;


        case 'update'://permet de modifié des attribut du film 
            
            if (isset($_POST['id'])) {
                // Collecter toutes les données reçues, excluant 'action' et 'id' pour la mise à jour
                $fieldsToUpdate = array_filter($_POST, function ($key) {
                    return !in_array($key, ['action', 'id']);
                }, ARRAY_FILTER_USE_KEY);

                // Si des champs valides sont à mettre à jour
                if (!empty($fieldsToUpdate)) {
                    $response = $film->updateMovieFields($_POST['id'], $fieldsToUpdate);
                } else {
                    $response = ['error' => true, 'message' => 'Aucun champ valide pour la mise à jour'];
                }
            } else {
                $response = ['error' => true, 'message' => 'ID du film manquant'];
            }
            break;

        case 'delete':
            if (isset($_POST['id'])) {
                $response = $film->delete(htmlspecialchars($_POST['id']));
            } else {
                $response = ['error' => true, 'message' => 'ID du film manquant'];
            }
            break;


        case 'addTagToFilm'://permet de rajouter un tag à un film
            if (isset($_POST['id_movie'], $_POST['tag_name'])) {
                $id_movie = htmlspecialchars($_POST['id_movie']);
                $tag_name = htmlspecialchars($_POST['tag_name']);


                $tag = new Tag();
                $existingTag = $tag->findByName($tag_name);

                if (!$existingTag) {
                    // Tag n'existe pas, donc on le crée
                    $result = $tag->add($tag_name);
                    if (!$result['success']) {
                        echo json_encode(['success' => false, 'message' => "Impossible d'ajouter le tag : " . $result['message']]);
                        exit;
                    }
                    $tag_id = $result['id'];
                } else {
                    $tag_id = $existingTag['id_tag'];
                }

                // Vérifier si le tag est déjà associé à ce film
                $existingTags = $tag->getExistTagByIdMovie($id_movie);
                $alreadyTagged = false;

                foreach ($existingTags as $existingTag) {
                    if ($existingTag['id_tag'] == $tag_id) {
                        $alreadyTagged = true;
                        break;
                    }
                }

                if ($alreadyTagged) {
                    echo json_encode(['success' => false, 'message' => 'Ce tag est déjà associé à ce film']);
                    exit;
                }

                // Associer le tag au film si pas déjà associé
                try {
                    $film->addTagToFilm($id_movie, $tag_id);
                    echo json_encode(['success' => true, 'message' => 'Tag ajouté au film avec succès', 'movie_id' => $id_movie, 'tag_id' => $tag_id, 'tag_name' => $tag_name]);
                    exit;
                } catch (Exception $e) {
                    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
                    exit;
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'ID du film ou nom du tag manquant']);
                exit;
            }
            break;

        case 'removeTag'://permet de supprimer un tag d'un film
            if (isset($_POST['id_movie'], $_POST['id_tag'])) {
                $id_movie = htmlspecialchars($_POST['id_movie']);
                $id_tag = htmlspecialchars($_POST['id_tag']);

                try {
                    $film = new Movie();
                    $film->removeTagFromFilm($id_movie, $id_tag);
                    echo json_encode(['success' => true, 'message' => 'Tag retiré du film avec succès']);
                } catch (Exception $e) {
                    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'ID du film ou ID du tag manquant']);
            }
            exit;
            break;

        case 'addActorToMovie'://permet de rajouter un acteur à un film

             if (isset($_POST['id_movie'], $_POST['id_actor'])) {
                 
                $filmId = htmlspecialchars($_POST['id_movie']);
                $actorId = htmlspecialchars($_POST['id_actor']);
    
                if (is_numeric($filmId) && is_numeric($actorId)) {
                    $movie->addActorToFilm($filmId, $actorId);
                    echo json_encode(['success' => true, 'message' => 'Acteur ajouté au film avec succès']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Invalid input data']);
                }
            }
            break;





        default:
            $response = ['error' => true, 'message' => 'Action non valide'];
            exit;
    }

    echo json_encode($response);
}
?>
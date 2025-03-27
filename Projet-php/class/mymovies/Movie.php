<?php
require_once 'Database.php';

class Movie {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll() {
        $sql = "SELECT * FROM Movie";
        try {
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function findById($id) {
        $sql = "SELECT * FROM Movie WHERE id_movie = :id";
        try {
            $stmt = $this->db->query($sql, ['id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function findByTitle($title) {
        $sql = "SELECT id_movie FROM Movie WHERE title = :title";
        try {
            $stmt = $this->db->query($sql, ['title' => $title]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function add($title, $releaseDate, $poster, $synopsis, $directorId, $seen, $rating, $tags, $actors): array {
        try {
            $sql = "INSERT INTO Movie (title, release_date, poster, synopsis, id_director, viewed, rating) VALUES (:title, :releaseDate, :poster, :synopsis, :directorId, :seen, :rating)";
            

            $param = [
                'title' => $title,
                'releaseDate' => $releaseDate,
                'poster' => $poster,
                'synopsis' => $synopsis,
                'directorId' => (int)$directorId,
                'seen' => $seen,
                'rating' => (int)$rating
            ];

            
            $stmt = $this->db->query($sql, $param); // Utilisation de `prepare` au lieu de `query` pour les préparations
            
            $filmId = $this->findByTitle($title); 

            foreach ($tags as $tagId) {
                $this->addTagToFilm($filmId['id_movie'], $tagId);
            }

            foreach ($actors as $actorId) {
                $this->addActorToFilm($filmId['id_movie'], $actorId);
            }

            return ['success' => true, 'message' => 'Film added successfully', 'id_movie' => $filmId['id_movie']];
        } catch (PDOException $e) {
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }


    public function addTagToFilm($filmId, $tagId) {
        $sql = "INSERT INTO TagOfMovie (id_movie, id_tag) VALUES (:filmId, :tagId)";
        $stmt = $this->db->query($sql,['filmId' => (int)$filmId, 'tagId' => (int)$tagId]);
    }

    public function removeTagFromFilm($filmId, $tagId) {
        $sql = "DELETE FROM TagOfMovie WHERE id_movie = :filmId AND id_tag = :tagId";
        $stmt = $this->db->query($sql, ['filmId' => (int)$filmId, 'tagId' => (int)$tagId]);
    }

    public function addActorToFilm($filmId, $actorId) {
        $sql = "INSERT INTO ActIn (id_movie, id_actor) VALUES (:filmId, :actorId)";
        $stmt = $this->db->query($sql,['filmId' => (int)$filmId, 'actorId' => (int)$actorId] );
    }

    public function removeActorFromFilm($filmId, $actorId) {
        $sql = "DELETE FROM ActIn WHERE id_movie = :filmId AND id_actor = :actorId";
        $stmt = $this->db->query($sql, ['filmId' => (int)$filmId, 'actorId' => (int)$actorId]);
    }


    public function updateMovieFields($id_movie, $fields) {
        // Construction de la chaîne SQL pour la mise à jour dynamique
        $sql = "UPDATE Movie SET ";
        $params = [];
        $set = [];

        // Ajout de chaque champ à mettre à jour dans la requête SQL
        foreach ($fields as $key => $value) {
            $set[] = "$key = :$key";
            $params[$key] = $value;
        }
        $sql .= implode(', ', $set);
        $sql .= " WHERE id_movie = :id_movie";
        $params['id_movie'] = $id_movie;

        // Exécution de la requête
        try {
            $this->db->query($sql, $params);
            return ['success' => true, 'message' => 'Movie updated successfully'];
        } catch (PDOException $e) {
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }

    public function delete($id): array {
        try {
            $sql = "DELETE FROM Movie WHERE id_movie = :id";
            $this->db->query($sql, ['id' => $id]);
            return ['success' => true, 'message' => 'Film deleted successfully'];
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function searchByTitle($title) {
        $sql = "SELECT * FROM Movie WHERE title LIKE :title";
        try {
            $stmt = $this->db->query($sql, ['title' => $title]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
?>

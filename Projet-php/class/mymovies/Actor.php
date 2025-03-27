<?php
require_once 'Database.php';

class Actor {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll() {
        $sql = "SELECT * FROM Actor";
        try {
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_CLASS, 'mymovies\PeopleCard');
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function getActorById($id_actor) {
        $sql = "SELECT * FROM Actor WHERE id_actor = ?";
        try {
            $stmt = $this->db->query($sql, [$id_actor]);
            $stmt->setFetchMode(PDO::FETCH_CLASS, 'mymovies\PeopleCard');
            $actor = $stmt->fetch();
            if (!$actor) {
                throw new Exception("Actor not found or could not be fetched as PeopleCard");
            }
            return $actor;
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function findByLastName($last_name) {
        $sql = "SELECT * FROM Actor WHERE last_name = :last_name";
        try {
            $stmt = $this.s->db->query($sql, ['last_name' => $last_name]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function findByFirstName($first_name) {
        $sql = "SELECT * FROM Actor WHERE first_name = :first_name";
        try {
            $stmt = $this->db->query($sql, ['first_name' => $first_name]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function findByName($first_name, $last_name) {
        $sql = "SELECT * FROM Actor WHERE first_name = :first_name AND last_name = :last_name";
        try {
            $stmt = $this->db->query($sql, ['first_name' => $first_name, 'last_name' => $last_name]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
        
            return ['error' => $e->getMessage()];
        }
    }

    public function add($last_name, $first_name, $photo): array {
        // Vérifier si l'acteur existe déjà
        $existingActor = $this->findByName($first_name, $last_name);
        if ($existingActor) {
            return ['error' => true, 'message' => 'Cet acteur existe déjà'];
        }

        // Ajout de l'acteur s'il n'existe pas
        $sql = "INSERT INTO Actor (last_name, first_name, photo) VALUES (:last_name, :first_name, :photo)";
        try {
            $this->db->query($sql, ['last_name' => $last_name, 'first_name' => $first_name, 'photo' => $photo]);

            // Récupérer les informations de l'acteur ajouté
            $newActor = $this->findByName($first_name, $last_name);
            if ($newActor) {
                return [
                    'success' => true,
                    'message' => 'Actor added successfully',
                    'id' => $newActor['id_actor'], 
                    'first_name' => $first_name,
                    'last_name' => $last_name 
                ];
            } else {
                return ['error' => true, 'message' => 'Failed to retrieve newly added actor'];
            }

        } catch (PDOException $e) {
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }

    public function update($id_actor, $last_name, $first_name, $photo): array {
        $sql = "UPDATE Actor SET last_name = :last_name, first_name = :first_name, photo = :photo WHERE id_actor = :id_actor";
        try {
            $this->db->query($sql, [
                'id_actor' => $id_actor,
                'last_name' => $last_name,
                'first_name' => $first_name,
                'photo' => $photo
            ]);
            return ['success' => true, 'message' => 'Actor updated successfully'];
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function delete($id_actor): array {
        $sql = "DELETE FROM Actor WHERE id_actor = :id_actor";
        try {
            $this->db->query($sql, ['id_actor' => $id_actor]);
            return ['success' => true, 'red' => 'A_iuii'];
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }




    public function getActorByIdMovie($id_movie) {
        $sql = "SELECT Actor.* FROM Actor INNER JOIN ActIn ON Actor.id_actor = ActIn.id_actor WHERE ActIn.id_movie = ?";
        try {
            $stmt = $this->db->query($sql, [$id_movie]);
            return $stmt->fetchAll(PDO::FETCH_CLASS, 'mymovies\PeopleCard');
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }



    public function updateActorFields($id_actor, $fields) {
        //permet de modifier des attributs de l'acteur
        //non utilisé par manque de temps
        // Construction de la chaîne SQL pour la mise à jour dynamique
        $sql = "UPDATE Actor SET ";
        $params = [];
        $set = [];

        // Ajout de chaque champ à mettre à jour dans la requête SQL
        foreach ($fields as $key => $value) {
            $set[] = "$key = :$key";
            $params[$key] = $value;
        }
        $sql .= implode(', ', $set);
        $sql .= " WHERE id_actor = :id_actor";
        $params['id_actor'] = $id_actor;

        // Exécution de la requête
        try {
            $this->db->query($sql, $params);
            return ['success' => true, 'message' => 'Actor updated successfully'];
        } catch (PDOException $e) {
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }


    public function searchByName($name) {
        $sql = "SELECT * FROM Actor WHERE last_name LIKE :name OR first_name LIKE :name";
        try {
            $stmt = $this->db->query($sql, ['name' => $name]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    
}
?>

<?php
require_once 'Database.php';

class Director {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll() {
        $sql = "SELECT * FROM Director";
        try {
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_CLASS, 'mymovies\PeopleCard');
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }



    public function getDirectorById($id_director) {
        $sql = "SELECT * FROM Director WHERE id_director = ?";
        try {
            $stmt = $this->db->query($sql, [$id_director]);
            $stmt->setFetchMode(PDO::FETCH_CLASS, 'mymovies\PeopleCard');
            $director = $stmt->fetch();
            if (!$director) {
                throw new Exception("Director not found or could not be fetched as PeopleCard");
            }
            return $director;
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }


    public function findByLastName($last_name) {
        $sql = "SELECT * FROM Director WHERE last_name = :last_name";
        try {
            $stmt = $this->db->query($sql, ['last_name' => $last_name]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function findByFirstName($first_name) {
        $sql = "SELECT * FROM Director WHERE first_name = :first_name";
        try {
            $stmt = $this->db->query($sql, ['first_name' => $first_name]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function findByName($first_name, $last_name) {
        $sql = "SELECT * FROM Director WHERE first_name = :first_name AND last_name = :last_name";
        try {
            $stmt = $this->db->query($sql, ['first_name' => $first_name, 'last_name' => $last_name]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function add($last_name, $first_name, $photo): array {
        // Vérifier si le directeur existe déjà
        $existingDirector = $this->findByName($first_name, $last_name);
        if ($existingDirector) {
            return ['error' => true, 'message' => 'Ce réalisateur existe déjà'];
        }

        // Ajout du directeur s'il n'existe pas
        $sql = "INSERT INTO Director (last_name, first_name, photo) VALUES (:last_name, :first_name, :photo)";
        try {
            $this->db->query($sql, ['last_name' => $last_name, 'first_name' => $first_name, 'photo' => $photo]);

            // Récupérer les informations du directeur ajouté
            $newDirector = $this->findByName($first_name, $last_name);
            if ($newDirector) {
                return [
                    'success' => true,
                    'message' => 'Director added successfully',
                    'id' => $newDirector['id_director'], 
                    'first_name' => $first_name,
                    'last_name' => $last_name 
                ];
            } else {
                return ['error' => true, 'message' => 'Failed to retrieve newly added director'];
            }

        } catch (PDOException $e) {
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }



    public function update($id_director, $last_name, $first_name, $photo): array {
        $sql = "UPDATE Director SET last_name = :last_name, first_name = :first_name, photo = :photo WHERE id_director = :id_director";
        try {
            $this->db->query($sql, [
                'id_director' => $id_director,
                'last_name' => $last_name,
                'first_name' => $first_name,
                'photo' => $photo
            ]);
            return ['success' => true, 'message' => 'Director updated successfully'];
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function delete($id_director): array {
        $sql = "DELETE FROM Director WHERE id_director = :id_director";
        try {
            $this->db->query($sql, ['id_director' => $id_director]);
            return ['success' => true, 'message' => 'Director deleted successfully'];
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }


    public function searchByName($name) {
        $sql = "SELECT * FROM Director WHERE last_name LIKE :name OR first_name LIKE :name";
        try {
            $stmt = $this->db->query($sql, ['name' => $name]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
?>

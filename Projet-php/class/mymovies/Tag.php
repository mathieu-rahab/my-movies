<?php

class Tag {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll() {
        $sql = "SELECT * FROM Tag";
        try {
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_CLASS, 'mymovies\TagCard');
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function getTagByIdMovie($id_movie) {
        $sql = "SELECT Tag.id_tag, Tag.name FROM Tag JOIN TagOfMovie ON Tag.id_tag = TagOfMovie.id_tag WHERE TagOfMovie.id_movie = ?";
        try {
            $stmt = $this->db->query($sql, [$id_movie]);
            return $stmt->fetchAll(PDO::FETCH_CLASS, 'mymovies\TagCard');
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }


    public function getExistTagByIdMovie($id_movie) {
        $sql = "SELECT Tag.id_tag, Tag.name FROM Tag JOIN TagOfMovie ON Tag.id_tag = TagOfMovie.id_tag WHERE TagOfMovie.id_movie = ?";
        try {
            $stmt = $this->db->query($sql, [$id_movie]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }



    
    public function findByName($name) {
        $sql = "SELECT * FROM Tag WHERE name = :name";
        try {
            $stmt = $this->db->query($sql, ['name' => $name]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function add($name): array {
        $sql = "INSERT INTO Tag (name) VALUES (:name)";
        try {
            $this->db->query($sql, ['name' => $name]);
            $tag = $this->findByName($name);
            return ['success' => true, 'message' => 'Tag added successfully', 'id' => $tag['id_tag'], 'name' => $tag['name']];
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function update($id, $name): array {
        $sql = "UPDATE Tag SET name = :name WHERE id_tag = :id";
        try {
            $this->db->query($sql, ['id' => $id, 'name' => $name]);
            return ['success' => true, 'message' => 'Tag updated successfully'];
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function delete($id): array {
        $sql = "DELETE FROM Tag WHERE id_tag = :id";
        try {
            $this->db->query($sql, ['id' => $id]);
            return ['success' => true, 'message' => 'Tag deleted successfully'];
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
?>

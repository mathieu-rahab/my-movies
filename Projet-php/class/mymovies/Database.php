<?php

class Database {


    private $port = '3306';
    private static $instance = null;
    private $pdo;

    // Constructeur privé pour empêcher la création directe d'instances
    private function __construct() {
        $envPath = __DIR__ . '/../../.env'; // Chemin du fichier .env
        $this->loadEnv($envPath);


        try {
            $dsn = 'mysql:host=' . $_ENV["host"] . ';port=' . $this->port . ';dbname=' . $_ENV["dbName"];
            $this->pdo = new PDO($dsn, $_ENV["user"], $_ENV["password"]);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo 'mysql:host=' . $_ENV["host"] . ';port=' . $this->port . ';dbname=' . $_ENV["dbName"];
            echo $dsn, $_ENV["user"], $_ENV["password"];

            echo '<div style="color: red"><b>!!! ERREUR DE CONNEXION !!!</b><br>';
            echo 'Code : ' . $e->getCode() . '<br>';
            echo 'Message : ' . $e->getMessage() . '</div>';
            die("-> Exécution stoppée <-");
        }
    }

    // Méthode pour obtenir l'instance unique de la classe
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    // Méthode pour exécuter des requêtes SQL
    public function query($sql, $params = []) {
        $stmt = $this->getPDO()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    // Méthode pour obtenir la connexion
    public function getPDO() :PDO {
        return $this->pdo;
    }

    // Empêcher le clonage de l'instance
    private function __clone() {}

    // Empêcher la désérialisation de l'instance
    public function __wakeup() {}

    private function loadEnv(string $file) {
        if (!file_exists($file)) {
            die("❌ Fichier .env introuvable ! Vérifiez son emplacement.");
        }

        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $_ENV[trim($key)] = trim($value);
            }
        }
    }
}
?>

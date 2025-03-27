<?php
class Auth {
    private $username = "admin"; // Identifiant en dur
    private $password = "password"; // Mot de passe en dur

    public function login($username, $password) {
        if(isset($username) && isset($password)){
            if (htmlspecialchars($username) === $this->username && htmlspecialchars($password) === $this->password) {
            $_SESSION['loggedin'] = true;
            return true;
            }
        }

        return false;
    }

    public function logout() {
        unset($_SESSION['loggedin']);
    }

    public function isLoggedIn() {
        return isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
    }
}
?>

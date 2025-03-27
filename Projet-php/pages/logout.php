<?php
session_start();
require '../class/mymovies/Auth.php';

$auth = new Auth();
$auth->logout();

header('Location: ../'); // Redirection après déconnexion

session_destroy();
exit();
?>

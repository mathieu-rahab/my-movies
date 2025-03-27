<?php
require __DIR__."/../config.php" ;
ob_start();
session_start();

require '../class/mymovies/Auth.php';

require $GLOBALS['PHP_DIR']."class/Autoloader.php" ;
Autoloader::register();
use mymovies\Template;

$auth = new Auth();
$message = "";
$lastPage = isset($_GET['lastPage']) ? $_GET['lastPage'] : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = htmlspecialchars($_POST['username']);
    $password = htmlspecialchars($_POST['password']);
    $lastPage = htmlspecialchars($_POST['lastPage']);

    if ($auth->login($username, $password)) {
        
            header('Location: ../'); // Redirection par défaut après connexion réussie
        
        exit();
    } else {
        $message = "Identifiant ou mot de passe incorrect";
    }
}
?>


<div id="block-login">
    
    <?php if($auth->isLoggedIn()){
        echo "<h2>Vous êtes déjà connecté</h2>";
    }
    else{?>
         <h2>Connexion</h2>
         <?php if ($message): ?>
            <p style="color: red;"><?php echo $message; ?></p>
         <?php endif; ?>
         <form method="post" action="">
             <input type="hidden" name="lastPage" value="<?php echo htmlspecialchars($lastPage); ?>">
             <input type="text" id="username" name="username" required placeholder="Identifiant">
             <br>
             <input type="password" id="password" name="password" required placeholder="Mot de passe">
             <br>
             <button type="submit">Se connecter</button>
         </form>
        
    <?php } ?>
    
</div>

<?php $code = ob_get_clean() ?>
<?php Template::render($code, 'styleLogin.css'); ?>

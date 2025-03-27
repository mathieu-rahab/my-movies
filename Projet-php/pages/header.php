
<!-- partie header à incoporé dans toutes le fichier header.php-->
<header>
    <nav class="navBar">
        <a href="/my-movies/Projet-php/" id="iconButton">
            <img src="<?= $GLOBALS['ICON_DIR'] ?>icons8-pop-corn-96.png" alt="icon" id="icon">
        </a>
        <a href="<?= $GLOBALS['DOCUMENT_DIR'] . 'pages/basicSearching.php'?> ">Recherche</a>
        <a href="<?= $GLOBALS['DOCUMENT_DIR'] . 'pages/advancedSearching.php'?> ">Recherche Complexe</a>

        <!-- a modifié en fonction de si connecté ou non-->
        <a href="<?= $GLOBALS['DOCUMENT_DIR'] . 'pages/addMovie.php'?>">Nouveau film</a>

        <?php 
        $auth = new Auth();
        // Vérifie si l'utilisateur est connecté
        $is_logged_in = $auth->isLoggedIn();
        if(!$is_logged_in){
            $current_url = $_SERVER['REQUEST_URI'];
            echo '<a href="'. $GLOBALS['DOCUMENT_DIR'] . 'pages/login.php?lastPage=' . urlencode($current_url) . '">Connexion</a>';//permet de rediriger vers la page de connexion (et en parametre pour retouré vers la page d'origine)
        }
        else{
            echo '<a href="'. $GLOBALS['DOCUMENT_DIR'] . 'pages/logout.php">Déconnexion</a>';
        }
        ?>
    </nav>
</header>

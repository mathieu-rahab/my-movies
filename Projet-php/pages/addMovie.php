<?php
require_once "../config.php";
session_start();
require '../class/mymovies/Auth.php';
require '../class/mymovies/Database.php';
require '../class/mymovies/Tag.php';
require '../class/mymovies/Actor.php';
require '../class/mymovies/Director.php';
require_once '../class/mymovies/TemplateForm.php';


require $GLOBALS['PHP_DIR']."class/Autoloader.php";
Autoloader::register();
use mymovies\Template;
use mymovies\TagCard;
use mymovies\People;

$auth = new Auth();
$is_logged_in = $auth->isLoggedIn();
if(!$is_logged_in){
    header('Location: ../pages/login.php');
    session_destroy();
    exit();
}

$title = $movies = null;
if(isset($_POST['title'])) {
    $title = htmlspecialchars($_POST['title']);
    $db = Database::getInstance();
    $statement = $db->query("SELECT id_movie, title, poster FROM Movie WHERE title = ?", [$title]);
    $movies = $statement->fetchAll(PDO::FETCH_CLASS, 'mymovies\MovieCard');
}

$tags = (new Tag())->getAll();
$directors = (new Director())->getAll();
$actors = (new Actor())->getAll();

ob_start();
?>

<div class="content">
    <?php if (!$title): //si l'utilisateur n'a pas encore transmis de titre ?>
        <h1>Nouveau film</h1>
        <form method="post" action="">
            <input type="text" name="title" placeholder="Titre du film" id="title" required>
            <button type="submit">Envoyer</button>
        </form>
    <?php else: ?>
        <?php if ($movies): //si ce film existe déjà?>
            <h2 style="color:white;">Ce film existe déjà</h2>
            <div class="films">
                <?php foreach ($movies as $movie): ?>
                    <?= $movie->getHTML(); ?>
                <?php endforeach; ?>
            </div>
        <?php else: //si à tranmis un titre et n'existe pas ?>
            <h1>Nouveau film: <h1 id="title"><?= $title ?></h1></h1>
            <div id="form">
                <input type="date" name="release_year" placeholder="Année de sortie"><br>
                <input type="text" name="synopsis" placeholder="Synopsis"><br>
                <input type="file" name="file" id="file" accept="image/*"><br>
                <div id="tags">
                    <h3>Tag</h3>
                    <button class="newItems" id="newTag">Nouveau tag</button><br>
                    <?php foreach ($tags as $tag): ?>
                        <?= $tag->getHTML_checkbox(); ?>
                    <?php endforeach; ?>
                </div>
                <div id="directors">
                    <h3>Réalisateur</h3>
                    <button class="newItems" id="newDirector">Nouveau réalisateur </button><br>
                    <?php foreach ($directors as $director): ?>
                        <?= $director->getHTML_checkbox('director'); ?>
                    <?php endforeach; ?>
                </div>
                <div id="actors">
                    <h3>Acteur</h3>
                    <button class="newItems" id="newActor">Nouveau acteur </button><br>
                    <?php foreach ($actors as $actor): ?>
                        <?= $actor->getHTML_checkbox('actor'); ?>
                    <?php endforeach; ?>
                </div>
                <button onclick="createFilm()">Ajouter le film</button>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?= TemplateForm::formPeople(); ?>

<?php
$code = ob_get_clean();
Template::render($code, ["styleIndex.css", "styleAddMovie.css"], "scriptAddMovie.js");
?>

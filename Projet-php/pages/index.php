<?php
require_once "../config.php";

session_start();

require '../class/mymovies/Auth.php';
require '../class/mymovies/Database.php';

require $GLOBALS['PHP_DIR']."class/Autoloader.php";
Autoloader::register();

use mymovies\Template;
use mymovies\MovieCard;

$db = Database::getInstance();
?>

<?php ob_start() ?>

<?php
$statementTags = $db->query("
    SELECT Tag.*, COUNT(TagOfMovie.id_tag) AS tag_count
    FROM Tag
    JOIN TagOfMovie ON Tag.id_tag = TagOfMovie.id_tag
    GROUP BY Tag.id_tag
    ORDER BY tag_count DESC;
");
$tags = $statementTags->fetchAll(PDO::FETCH_ASSOC);
?>

<?php foreach ($tags as $tag): ?>

    <div class="categorie">
        <h2 class="label" id="<?= $tag['id_tag'] ?>"><?= htmlspecialchars($tag['name']) ?></h2><!--nom du label--><!--id correpond à l'id du tag-->
        <div class="films">
            <!--boutton qui permet de ce déplacé dans la catégorie sur ordi-->
            <div class="contenerButtonTranslate left">
                <span>&#8249;</span>
            </div>

            <!--ajout de film dans la catégorie (autant que nécessaire)-->
            <?php
            $statement = $db->query("SELECT DISTINCT Movie.id_movie, Movie.title, Movie.poster
             FROM Movie
             JOIN TagOfMovie ON Movie.id_movie = TagOfMovie.id_movie
             WHERE TagOfMovie.id_tag = ?", [$tag['id_tag']]);
            $movies = $statement->fetchAll(PDO::FETCH_CLASS, 'mymovies\MovieCard');
            ?>

            <?php foreach ($movies as $movie): ?>
                <?= $movie->getHTML() ?>
            <?php endforeach; ?>

            <!--boutton qui permet de ce déplacé dans la catégorie sur ordi-->
            <div class="contenerButtonTranslate right">
                <span>&#8250;</span>
            </div>
        </div>
    </div>

<?php endforeach; ?>

<?php $code = ob_get_clean(); ?>
<?php Template::render($code, "styleIndex.css", "scriptIndex.js"); ?>

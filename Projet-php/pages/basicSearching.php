<?php
require_once "../config.php";
require '../class/mymovies/Auth.php';

require $GLOBALS['PHP_DIR']."class/Autoloader.php";
Autoloader::register();


use mymovies\Template;

?>
<?php ob_start() ?>


<div class="container">
    <h1 style="color:white">Search for Movies, Actors, or Directors</h1>
    <form id="searchForm">
        <label for="Type de recherche"></label><select id="searchType">
            <option value="actor">Actor</option>
            <option value="director">Director</option>
            <option value="movie">Movie</option>
        </select>
        <label for="searchQuery"></label><input type="text" id="searchQuery" placeholder="Entrer un nom">
        <button type="submit">Search</button>
    </form>
    <div id="results"></div>
</div>


<?php $code = ob_get_clean(); ?>
<?php Template::render($code, "styleBasicSearch.css", "simpleSearch.js"); ?>

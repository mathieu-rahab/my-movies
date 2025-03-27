<?php
require_once "../config.php";
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


?>
<?php ob_start() ?>
?>


<div class="container">
    <h1>Advanced Search</h1>
    <form id="searchForm">
        <select id="type">
            <option value="actor">Actor</option>
            <option value="director">Director</option>
            <option value="movie">Movie</option>
        </select>
        <input type="text" id="title" placeholder="Search by title, name, or ID...">
        <select id="logic">
            <option value="AND">AND</option>
            <option value="OR">OR</option>
        </select>
        <button type="submit">Search</button>
    </form>
    <div id="results"></div>
</div>


<?php $code = ob_get_clean(); ?>
<?php Template::render($code, "styleBasicSearch.css", "advancedSearch.js"); ?>

<?php
namespace mymovies;

class MovieCard
{

    public $id_movie;
    public $poster;
    public $title;

    public function getHTML()
    {
        ?>

        <a class="film" id="<?= $this->id_movie ?>" href="<?php echo $GLOBALS['DOCUMENT_DIR'] .'pages/movie.php?idMovie='. $this->id_movie ?>"> <!-- id correspondant à l'id du film -->
            <img class="imgFilm" src="<?php echo $GLOBALS['POSTER_DIR'] . $this->poster ?>" alt="<?= $this->title?>"> <!--ajout de l'image-->
        </a>
        <?php
    }
}
?>

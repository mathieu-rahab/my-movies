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
use mymovies\PeopleCard;




if(isset($_GET['idMovie'])){
  $id_movie = htmlspecialchars($_GET['idMovie']) ;
  $db = Database::getInstance();
    $statement = $db->query("SELECT id_movie, title, DATE_FORMAT(release_date, '%d-%m-%Y') AS release_date, poster, synopsis, id_director, viewed, rating FROM Movie WHERE id_movie = ?", [$id_movie]);

  $movie = $statement->fetch(PDO::FETCH_ASSOC);
  if(isset($movie)){
    ob_start() ?>
    
    <div class="content">
        <div class="bloc1">
            <img class="imgFilm" src="<?php echo $GLOBALS['POSTER_DIR'] . $movie['poster'] ?>" alt="<?= $movie['title']?>">
            <div class="info">
                <!--si utilisateur connécté-->
              <?php 
              $auth = new Auth();
              // Vérifie si l'utilisateur est connecté
              $is_logged_in = $auth->isLoggedIn();
              if($is_logged_in){?>
                <div id="adminTools">
                    <button id="updateMovie">
                        <img src="<?= $GLOBALS['ICON_DIR'] ?>icons8-modifier-64.png" alt="delete movie">
                    </button>
                    
                    
                    
                </div>
              <?php } ?>


                <h2 id="titleFilm" class="<?php if($is_logged_in){echo 'update';}?>">
                    <?= $movie['title']?>
                </h2>
                <span>Sortie le </span>
                <span id="realiseDate" class="<?php if($is_logged_in){echo 'update';}?>"><?= $movie['release_date'] ?></span>
                <div class="tags">
                    
                  <?php
                   $tag = new Tag();
                    $tags = $tag->getTagByIdMovie($id_movie);
                    ?>

                  <?php foreach ($tags as $tag): ?>
                      <?= $tag->getHTML($is_logged_in) ?>
                  <?php endforeach; ?>


                </div>
                <div id="synopsisBlock">
                    <h2 >Synopsis</h2>
                    <p id="synopsis" class="<?php if($is_logged_in){echo 'update';}?>"> <?= $movie['synopsis']?> </p>
                </div>

                <!-- Pour crée l'url : "https://www.justwatch.com/fr/recherche?q=" + le titre du film -->
                <a id="justWatch" target="_blank" href="<?= 'https://www.justwatch.com/fr/recherche?q=' . $movie['title']?>">Où regarder ?</a>


                <div id="userInfo">
                    <div class="content_toogle">
          
                        <div id="toogle" class="<?php if($movie['viewed']){echo 'seen';}?>"></div><!-- ajouté la classe "seen" si le film à était vu -->
                        <span id="toSeen">À voir</span>
                        <span id="seen" >Vu</span>
                    </div>

                    <?php if($movie['viewed']): ?>
                        <div id="star-rating">
                            <?php for($i = 1; $i <= 5; $i++): ?>
                                <span class="star<?php if($movie['rating'] >= $i) { echo ' note'; } ?>" data-value="<?= $i ?>">★</span>
                            <?php endfor; ?>
                        </div>
                    <?php endif; ?>
 
                    
                </div>


            </div>
        </div>


        <div id="distribution">
            <hr>
            <h2>Distribtion</h2>

            <div class="contentReal">
                <h3>Réalisateur</h3>
                <div class="real">
            
                    <?php
                    $d = new Director();
                    $director = $d->getDirectorById($movie['id_director']);
                    $director->getHTML('director', $is_logged_in);
                    ?>
                </div>


            </div>
            <div class="contentacteur">
                <h3>Acteur</h3>
                <div class="acteur">

                    <?php
                        $actor = new Actor();
                        $actors = $actor->getActorByIdMovie($id_movie);
                      ?>

                      <?php foreach ($actors as $actor): ?>
                          <?= $actor->getHTML('actor', $is_logged_in) ?>
                      <?php endforeach; ?>


                </div>
            </div>

        </div>
    </div>





        <?php if($is_logged_in){//si l'utilisateur est connecté, ajoute le template du formulaire pour ajout de nouveaux acteurs
            echo TemplateForm::formPeople();   
        }    ?>
                      
               




    <?php $code = ob_get_clean(); ?>
    <?php Template::render($code, "styleMovie.css", "scriptMovie.js"); 
  }

}
else{
  echo "non";
}
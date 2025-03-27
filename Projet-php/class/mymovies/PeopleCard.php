<?php
namespace mymovies;

class PeopleCard
{
    public $id_people;
    public $id_actor;
    public $id_director;

    public $first_name;
    public $last_name;
    public $photo;

    public function getHTML($type, $is_logged_in)//'actor' or 'director'
    {

        if($type == 'actor'){
            $this->id_people = $this->id_actor;
        }
        else{
            $this->id_people = $this->id_director;
        }
        ?>
        <div id="<?=$this->id_people?>" class="PeopleCard <?=$type?> <?php if($is_logged_in){echo 'update';}?>">
            
            <img src="<?=  $GLOBALS['PEOPLE_DIR'] . $this->photo?>" alt="">

            <span><?= $this->first_name . ' ' . $this->last_name?></span>
        </div>
    <?php
    }

    public function getHTML_checkbox($type)//'actor' or 'director'
    {
        if($type == 'actor'){
            $this->id_people = $this->id_actor;
        }
        else{
            $this->id_people = $this->id_director;
        }
        ?>
        <div class="PeopleCard <?=$type?>">
            <input type="checkbox" id="<?=$type?>_<?=$this->id_people?>" name="<?=$type?>" value="<?=$this->id_people?>">
            <label for="<?=$type?>_<?=$this->id_people?>">
                <img src="<?= $GLOBALS['PEOPLE_DIR'] . $this->photo?>" alt="">
                <span><?= $this->first_name . ' ' . $this->last_name?></span>
            </label>
        </div>
        <?php
    }

}
<?php
namespace mymovies;

class TagCard
{

    public $id_tag;
    public $name;

    public function getHTML($is_logged_in)
    {
        ?>
        <span class="tag <?php if($is_logged_in){echo 'update';}?>" id="<?= $this->id_tag ?>"><?php echo htmlspecialchars($this->name) ?></span>
    <?php
    }

    public function getHTML_checkbox()
    {
        ?>
        <span class="tag" id="<?= $this->id_tag ?>">
            <input type="checkbox" id="checkbox_<?= $this->id_tag ?>" name="tag" value="<?= $this->id_tag ?>">
            <label for="checkbox_<?= $this->id_tag ?>"><?php echo htmlspecialchars($this->name) ?></label>
        </span>
        <?php
    }
}
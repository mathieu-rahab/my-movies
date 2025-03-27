<?php

namespace mymovies;

class Template
{
    public static function render($code, $css = null, $script = null): void
    { ?>
        <!doctype html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport"
                  content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
            <meta http-equiv="X-UA-Compatible" content="ie=edge">
            <title>Film</title>

            <link rel="stylesheet" href="<?php echo $GLOBALS['CSS_DIR'] ?>styleGlobal.css">
            <?php
            if (isset($css)) {
                if (is_array($css)) {
                    foreach ($css as $stylesheet) {
                        echo "<link rel='stylesheet' href='" . $GLOBALS['CSS_DIR'] . $stylesheet . "'>";
                    }
                } else {
                    echo "<link rel='stylesheet' href='" . $GLOBALS['CSS_DIR'] . $css . "'>";
                }
            }
            ?>

            
        </head>
        <body>
        <?php include $GLOBALS['PHP_DIR'] . "pages/header.php"; ?>
        <div id="main-content">
            <?php echo $code ?>
        </div> 
        <?php include $GLOBALS['PHP_DIR'] . "pages/footer.php"; ?>

        <?php if (isset($script)) {
            echo "<script src=" . $GLOBALS['SCRIPT_DIR'] . $script . "></script>";
        }?>

        </body>
        </html>
    <?php }
}

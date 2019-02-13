<?php
/* @var $this yii\web\View */
/* @var $files array */

?>

    <span class="fa fa-paperclip"></span>
    <?php foreach ($files as $file) {
        echo ' <a href = "' . $file['link'] . '" target="_blank" class="attachments-file">' . $file['name'] . '</a>';
    } ?>
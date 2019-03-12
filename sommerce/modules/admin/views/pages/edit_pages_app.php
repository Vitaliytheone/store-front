<?php

use \sommerce\assets\PagesAppAsset;

/* @var $this \yii\web\View */
/* @var $appConfig array */

PagesAppAsset::register($this);

?>

<script>
    window.appConfig = {};
    window.appConfig = <?= json_encode($appConfig); ?>
</script>

<div id="styles"></div>
<div id="root"></div>
<?php

use store\assets\ThemesCustomizerAsset;

/* @var $this \yii\web\View */
/* @var $urls array */

ThemesCustomizerAsset::register($this);

?>

<div data-customizer="<?= htmlspecialchars(json_encode($urls)) ?>" id="root"></div>
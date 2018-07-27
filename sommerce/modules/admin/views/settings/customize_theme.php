<?php

use sommerce\assets\ThemesCustomizerAsset;
use yii\helpers\Json;

/* @var $this \yii\web\View */
/* @var $urls array */

ThemesCustomizerAsset::register($this);

?>

<div data-customizer="<?= htmlspecialchars(Json::encode($urls)) ?>" id="root"></div>
<?php

use sommerce\assets\AdminProductsAssets;

/* @var $this \yii\web\View */
/* @var $endPoints array */

AdminProductsAssets::register($this);
?>

<script>
    window.appConfig = {};
    window.appConfig.api_endpoints = <?= json_encode($endPoints); ?>
</script>

<div id="root"></div>
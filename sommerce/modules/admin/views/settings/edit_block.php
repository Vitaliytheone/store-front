<?php
    /* @var $this \yii\web\View */
    /* @var $code string */
?>
<div class="m-grid m-grid--hor m-grid--root m-page bm">
    <?= $this->render('layouts/blocks/_edit_block_header'); ?>

    <?= $this->render('layouts/blocks/_edit_' . $code . '_block'); ?>
</div>
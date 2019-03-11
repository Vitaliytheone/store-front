<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Html;

$this->title = $name;
?><div class="container">
    <div class="row">
    <div class="col-lg-8">
            <h1><?= Html::encode($this->title) ?></h1>

            <div class="alert alert-danger">
                <?= nl2br(Html::encode($message)) ?>
            </div>

            <p>
                <?= Yii::t('app/superadmin', 'site.error.first_part') ?>
            </p>
            <p>
                <?= Yii::t('app/superadmin', 'site.error.second_part') ?>
            </p>
    </div>
    </div>
</div>
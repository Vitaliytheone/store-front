<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use frontend\assets\AdminAsset;
use frontend\assets\MetronicAsset;
use frontend\assets\JqueryUiAsset;
use frontend\assets\DragsortAsset;
use frontend\assets\ToastrAsset;
use frontend\assets\TextareaAutosizeAsset;
use frontend\assets\SwiperAsset;
use frontend\assets\RatingAsset;

AdminAsset::register($this);
MetronicAsset::register($this);
JqueryUiAsset::register($this);
RatingAsset::register($this);
DragsortAsset::register($this);
SwiperAsset::register($this);
TextareaAutosizeAsset::register($this);
ToastrAsset::register($this);
?>
<?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
    </head>

    <body style="background: #f2f3f8;">
    <?php $this->beginBody() ?>

    <?= $content ?>

    <?php $this->endBody() ?>
    </body>
    </html>
<?php $this->endPage() ?>
<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use store\assets\AdminAsset;
use store\assets\MetronicAsset;
use store\assets\JqueryUiAsset;
use store\assets\DragsortAsset;
use store\assets\ToastrAsset;
use store\assets\TextareaAutosizeAsset;
use store\assets\SwiperAsset;
use store\assets\RatingAsset;

TextareaAutosizeAsset::register($this);
AdminAsset::register($this);
MetronicAsset::register($this);
JqueryUiAsset::register($this);
RatingAsset::register($this);
DragsortAsset::register($this);
SwiperAsset::register($this);
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
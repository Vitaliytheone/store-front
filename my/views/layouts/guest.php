<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use my\assets\AppAsset;

AppAsset::register($this);

$this->context->addModule('indexController');
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
    </head>
    <body>
        <?php $this->beginBody() ?>

        <?= $content ?>

        <?php $this->endBody() ?>

        <?php if (YII_ENV_PROD) : ?>
            <?= $this->render('_metrika') ?>
        <?php endif; ?>
    </body>
</html>
<?php $this->endPage() ?>
<?php

    /* @var $this \yii\web\View */
    /* @var $content string */

    use yii\helpers\Html;
    use control_panel\assets\SuperAdminV2Asset;

SuperAdminV2Asset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
    <head>
        <?= Html::csrfMetaTags() ?>
        <meta charset="<?= Yii::$app->charset ?>">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
    </head>
    <body>
    <?php $this->beginBody() ?>

        <?= $this->render('_header.php') ?>

    <div class="container-fluid">
        <?= $content ?>
    </div>

    <?php if (isset($this->blocks['modals'])): ?>
        <?= $this->blocks['modals'] ?>
    <?php endif; ?>

    <?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>
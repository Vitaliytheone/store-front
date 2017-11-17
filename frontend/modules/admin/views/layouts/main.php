<?php

    /* @var $this \yii\web\View */
    /* @var $content string */

    use yii\helpers\Html;
    use frontend\assets\AdminAsset;
    use frontend\assets\MetronicAsset;

    AdminAsset::register($this);
    MetronicAsset::register($this);
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

<div class="m-grid m-grid--hor m-grid--root m-page">
    <?= $this->render('_header')?>
    <div class="container-fluid">
        <?= $content ?>
    </div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>

<!-- App messages script -->
<script>
    var jsonMessages = '<?= json_encode(Yii::$app->session->getFlash('messages')) ?>';
    window.app__messages = JSON.parse(jsonMessages);
</script>
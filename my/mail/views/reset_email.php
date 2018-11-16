<?php
    /* @var $customer \common\models\panels\Customers */

    use yii\bootstrap\Html;
    use my\helpers\Url;

    $url = Url::toRoute('/reset/' . $customer->token, true);
?>
<?= Html::a($url, $url) ?>

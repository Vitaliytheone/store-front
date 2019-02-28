<?php
    /* @var $customer \common\models\sommerces\Customers */

    use yii\bootstrap\Html;
    use control_panel\helpers\Url;

    $url = Url::toRoute('/reset/' . $customer->token, true);
?>
<?= Html::a($url, $url) ?>

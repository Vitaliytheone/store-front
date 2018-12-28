<?php
    use common\models\gateways\PaymentMethods;

    /* @var $method string */
    /** @var \common\models\gateways\Sites $gateway */

    $gateway = Yii::$app->gateway->getInstance();
?>

<?php
    switch ($method) {
        case PaymentMethods::METHOD_PAYPAL:
            echo $this->render('rules/_paypal', ['gateway' => $gateway]);
        break;
    }
?>
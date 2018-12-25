<?php

use \common\models\stores\PaymentMethods;

/* @var $method string */
/** @var \common\models\stores\Stores $store */

$store = Yii::$app->store->getInstance();

?>

<?php
    switch ($method) {
        case PaymentMethods::METHOD_PAYPAL:
            echo $this->render('rules/_paypal', ['store' => $store]);
            break;

        case PaymentMethods::METHOD_2CHECKOUT:
            echo $this->render('rules/_2checkout', ['store' => $store]);
            break;

        case PaymentMethods::METHOD_COINPAYMENTS:
            echo $this->render('rules/_coinpayments', ['store' => $store]);
            break;

        case PaymentMethods::METHOD_WEBMONEY:
            echo $this->render('rules/_webmoney', ['store' => $store]);
            break;

        case PaymentMethods::METHOD_YANDEX_MONEY:
            echo $this->render('rules/_yandex_money', ['store' => $store]);
            break;

        case PaymentMethods::METHOD_STRIPE:
            echo $this->render('rules/_stripe', ['store' => $store]);
            break;

        case PaymentMethods::METHOD_YANDEX_CARDS:
            echo $this->render('rules/_yandex_money', ['store' => $store]);
            break;

        case PaymentMethods::METHOD_PAGSEGURO:
            echo $this->render('rules/_pagseguro', ['store' => $store]);
            break;

        case PaymentMethods::METHOD_FREE_KASSA:
            echo $this->render('rules/_free_kassa', ['store' => $store]);
            break;

        case PaymentMethods::METHOD_PAYTR:
            echo $this->render('rules/_paytr', ['store' => $store]);
            break;

        case PaymentMethods::METHOD_PAYWANT:
            echo $this->render('rules/_paywant', ['store' => $store]);
            break;

        case PaymentMethods::METHOD_BILLPLZ:
            echo $this->render('rules/_billplz', ['store' => $store]);
            break;
    }
?>
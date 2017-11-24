<?php

use yii\helpers\ArrayHelper;
use frontend\modules\admin\components\Url;
use common\models\stores\PaymentMethods;
use frontend\helpers\UiHelper;


/* @var $paymentMethods[] \frontend\modules\admin\models\forms\EditPaymentMethodForm */

$imgPath = '/img/';

$methodItemsData = [
    PaymentMethods::METHOD_PAYPAL => [
        'icon' => $imgPath . 'paypal.png',
        'title' => Yii::t('admin', 'settings.payments_method_paypal'),
        'edit_button_title' => Yii::t('admin', 'settings.payments_edit_method'),
    ],
    PaymentMethods::METHOD_2CHECKOUT => [
        'icon' => $imgPath . '2checkout.png',
        'title' => Yii::t('admin', 'settings.payments_method_2checkout'),
        'edit_button_title' => Yii::t('admin', 'settings.payments_edit_method'),
    ],
    PaymentMethods::METHOD_BITCOIN => [
        'icon' => $imgPath . 'bitcoin.png',
        'title' => Yii::t('admin', 'settings.payments_method_bitcoin'),
        'edit_button_title' => Yii::t('admin', 'settings.payments_edit_method'),
    ],
];

/**
 * Return additional data like `icon`, `caption` for `payment method`
 * @param $method
 * @param $field
 * @return string
 */
$getMethodData = function($method, $field) use ($methodItemsData) {
    return ArrayHelper::getValue($methodItemsData, "$method.$field", $field);
};

?>

<!-- BEGIN: Subheader -->
<div class="m-subheader ">
    <div class="d-flex align-items-center">
        <div class="mr-auto">
            <h3 class="m-subheader__title">
                <?= Yii::t('admin', 'settings.payments_title') ?>
            </h3>
        </div>
    </div>
</div>
<!-- END: Subheader -->
<div class="m-content">

    <?php foreach ($paymentMethods as $method): ?>
        <div class="sommerce-settings__payment-cart m-portlet">
            <div class="row align-items-center">
                <div class="col-2">
                    <div class="payment-cart__preview">
                        <img src="<?= $getMethodData($method->method, 'icon') ?>" alt="" class="img-fluid">
                    </div>
                </div>
                <div class="col-10">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="payment-cart__title">
                                <?= $getMethodData($method->method, 'title') ?>
                            </div>
                        </div>
                        <div>
                            <div class="payment-cart__active">
                           <span class="m-switch m-switch--outline m-switch--icon m-switch--primary">
                                <label>
                                    <input class="toggle-active" type="checkbox" name="toggle-active"
                                        <?= UiHelper::toggleString($method->active, 'checked') ?>
                                            data-payment_method="<?= $method->method ?>"
                                            data-action_url="<?= Url::to([
                                                'settings/payments-toggle-active',
                                                'method' => $method->method,
                                            ])?>"
                                    >
                                    <span></span>
                                </label>
                            </span>
                            </div>
                            <div class="payment-cart__actions">
                                <a href="<?= Url::toRoute(['/settings/payments-settings', 'method'=> $method->method]) ?>"
                                   class="btn m-btn--pill m-btn--air btn-primary">
                                    <?= $getMethodData($method->method, 'edit_button_title') ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

</div>

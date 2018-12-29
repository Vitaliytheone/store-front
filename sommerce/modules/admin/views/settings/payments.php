<?php

use sommerce\assets\NavigationPaymentAsset;
use sommerce\modules\admin\components\Url;

/* @var $this \yii\web\View */
/* @var $method string Current `settings payments` method */
/* @var $submitUrl string */
/* @var $cancelUrl string */
/* @var $methodName string */
/* @var $paymentModel \sommerce\modules\admin\models\forms\EditPaymentMethodForm */
/* @var $paymentMethods[] \sommerce\modules\admin\models\forms\EditPaymentMethodForm */
/* @var $availableMethods array */
/* @var $paymentData array */

NavigationPaymentAsset::register($this);
?>
<div class="m-grid__item m-grid__item--fluid m-grid m-grid--hor-desktop m-grid--desktop m-body">
    <div class="m-grid__item m-grid__item--fluid  m-grid m-grid--ver	m-container m-container--responsive m-container--xxl m-page__container">
        <button class="m-aside-left-close m-aside-left-close--skin-light" id="m_aside_left_close_btn">
            <i class="la la-close"></i>
        </button>
        <div id="m_aside_left" class="m-grid__item m-aside-left ">
            <?= $this->render('layouts/_left_menu', [
                'active' => 'payments'
            ])?>
        </div>
        <div class="m-grid__item m-grid__item--fluid m-wrapper">
            <?php
                if (isset($method)) {
                    echo $this->render('layouts/payments/_edit_payment_method', [
                        'paymentModel' => $paymentModel,
                        'submitUrl' => Url::toRoute(['/settings/payments-settings', 'method' => $method]),
                        'cancelUrl' => Url::toRoute(['/settings/payments']),
                        'paymentData' => $paymentData,
                    ]);

                } else {
                    echo $this->render('layouts/payments/_methods_list', [
                        'paymentMethods' => $paymentMethods,
                        'availableMethods' => $availableMethods,
                    ]);
                }
            ?>
        </div>
    </div>
</div>
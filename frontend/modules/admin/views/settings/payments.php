<?php

use yii\helpers\Url;
use common\models\stores\PaymentMethods;

/* @var $this \yii\web\View */
/* @var $method string Current `settings payments` method */
/* @var $submitUrl string */
/* @var $cancelUrl string */
/* @var $paymentModel \frontend\modules\admin\models\forms\EditPaymentMethodForm */
/* @var $paymentMethods[] \frontend\modules\admin\models\forms\EditPaymentMethodForm */

$this->title = Yii::t('admin', 'settings.section_payments_page_title');

?>
<!-- begin::Body -->
<div class="m-grid__item m-grid__item--fluid m-grid m-grid--hor-desktop m-grid--desktop m-body">
    <div class="m-grid__item m-grid__item--fluid  m-grid m-grid--ver	m-container m-container--responsive m-container--xxl m-page__container">
        <!-- BEGIN: Left Aside -->
        <button class="m-aside-left-close m-aside-left-close--skin-light" id="m_aside_left_close_btn">
            <i class="la la-close"></i>
        </button>
        <div id="m_aside_left" class="m-grid__item m-aside-left ">
            <?= $this->render('layouts/_left_menu', [
                'active' => 'payments'
            ])?>
        </div>
        <!-- END: Left Aside -->
        <div class="m-grid__item m-grid__item--fluid m-wrapper">
            <?php
                /* Render methods list */
                if (!isset($method)) {
                    echo $this->render('layouts/payments/_methods_list', [
                        'paymentMethods' => $paymentMethods,
                    ]);
                    return;
                }

                /* Render method settings */
                $submitUrl = Url::to(['settings/payments-settings', 'method' => $method]);
                $cancelUrl = Url::to(['settings/payments']);

                switch ($method) {
                    case PaymentMethods::METHOD_PAYPAL:
                        echo $this->render('layouts/payments/_edit_paypal', [
                            'paymentModel' => $paymentModel,
                            'submitUrl' => $submitUrl,
                            'cancelUrl' => $cancelUrl,
                        ]);
                        break;
                    case PaymentMethods::METHOD_2CHECKOUT:
                        echo $this->render('layouts/payments/_edit_2checkout', [
                            'paymentModel' => $paymentModel,
                            'submitUrl' => $submitUrl,
                            'cancelUrl' => $cancelUrl,
                        ]);
                        break;
                    case PaymentMethods::METHOD_BITCOIN:
                        echo $this->render('layouts/payments/_edit_bitcoin', [
                            'paymentModel' => $paymentModel,
                            'submitUrl' => $submitUrl,
                            'cancelUrl' => $cancelUrl,
                        ]);
                        break;
                }
            ?>
        </div>
    </div>
</div>
<!-- end::Body -->
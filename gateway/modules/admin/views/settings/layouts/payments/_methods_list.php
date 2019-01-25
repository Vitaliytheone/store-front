<?php

use admin\components\Url;
use gateway\helpers\UiHelper;

/* @var $paymentMethods[] \admin\models\search\PaymentMethodsSearch */
/* @var $method \admin\models\search\PaymentMethodsSearch */

?>

<div class="m-subheader ">
    <div class="d-flex align-items-center">
        <div class="mr-auto">
            <h3 class="m-subheader__title">
                <?= Yii::t('admin', 'settings.payments_title') ?>
            </h3>
        </div>
    </div>
</div>

<div class="m-content">

    <?php foreach ($paymentMethods as $method): ?>

        <div class="gateway-settings__payment-cart m-portlet">
            <div class="row align-items-center">
                <div class="col-12">
                    <div class="payment-cart__preview">
                        <img src="<?= $method['icon'] ?>" alt="" class="img-fluid" style="<?= $method['icon_style'] ?>">
                    </div>
                    <div class="payment-cart__title">
                        <?= $method['title'] ?>
                    </div>
                    <div class="payment-cart__control d-flex justify-content-between align-items-center">
                        <div>
                            <div class="payment-cart__active">
                                 <span class="m-switch m-switch--outline m-switch--icon m-switch--primary">
                                     <label>
                                        <input class="toggle-active" type="checkbox"
                                               name="toggle-active" <?= UiHelper::toggleString($method['active'], 'checked') ?>
                                               data-payment_method="<?= $method['method'] ?>"
                                               data-action_url="<?= Url::toRoute(['/settings/payments-toggle-active', 'method' => $method['method'],]) ?>">
                                         <span></span>
                                     </label>
                                  </span>
                            </div>
                            <div class="payment-cart__actions">
                                <a href="<?= Url::toRoute(['/settings/payments-settings', 'method' => $method['method']]) ?>"
                                   class="btn m-btn--pill m-btn--air btn-primary">
                                    <?= Yii::t('admin', 'settings.payments_edit_method') ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <?php endforeach; ?>

</div>

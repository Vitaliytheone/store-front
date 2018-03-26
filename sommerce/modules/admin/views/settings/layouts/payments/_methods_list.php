<?php

use sommerce\modules\admin\components\Url;
use sommerce\helpers\UiHelper;

/* @var $paymentMethods[] \sommerce\modules\admin\models\forms\EditPaymentMethodForm */

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

        <?php /* @var $method \sommerce\modules\admin\models\forms\EditPaymentMethodForm */ ?>

        <div class="sommerce-settings__payment-cart m-portlet">
            <div class="row align-items-center">
                <div class="col-2">
                    <div class="payment-cart__preview">
                        <img src="<?= $method->getMethodsListItemData('icon' ) ?>" alt="" class="img-fluid">
                    </div>
                </div>
                <div class="col-10">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="payment-cart__title">
                                <?= $method->getMethodsListItemData('title' ) ?>
                            </div>
                        </div>
                        <div>
                            <div class="payment-cart__active">
                           <span class="m-switch m-switch--outline m-switch--icon m-switch--primary">
                                <label>
                                    <input class="toggle-active" type="checkbox" name="toggle-active" <?= UiHelper::toggleString($method->active, 'checked') ?> data-payment_method="<?= $method->method ?>" data-action_url="<?= Url::toRoute(['/settings/payments-toggle-active', 'method' => $method->method, ])?>">
                                    <span></span>
                                </label>
                            </span>
                            </div>
                            <div class="payment-cart__actions">
                                <a href="<?= Url::toRoute(['/settings/payments-settings', 'method'=> $method->method]) ?>" class="btn m-btn--pill m-btn--air btn-primary">
                                    <?= $method->getMethodsListItemData('edit_button_title' ) ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <?php endforeach; ?>

</div>

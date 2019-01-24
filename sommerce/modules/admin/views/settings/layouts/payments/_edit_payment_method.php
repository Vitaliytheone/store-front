<?php

use sommerce\modules\admin\widgets\PaymentSettingsForm;

/* @var $submitUrl string */
/* @var $cancelUrl string */
/* @var $paymentData array */
/* @var $paymentModel \sommerce\modules\admin\models\forms\EditPaymentMethodForm; */

$this->context->addModule('adminPayments');
?>

<div class="m-subheader ">
    <div class="d-flex align-items-center">
        <div class="mr-auto">
            <h3 class="m-subheader__title">
                <?= Yii::t('admin', "settings.payments_edit_method") . ' ' . $paymentData['name'] ?>
            </h3>
        </div>
    </div>
</div>

<div class="m-content">

    <div class="sommerce-settings__well">
        <div class="row align-items-center">
            <div class="col-md-3 text-center">
                <img src="<?= $paymentData['icon'] ?>" alt="" class="img-fluid">
            </div>
            <div class="col-md-9">
                <?= $paymentData['description'] ?>
            </div>
        </div>
    </div>

    <?= PaymentSettingsForm::widget([
        'paymentModel' => $paymentModel,
        'submitUrl' => $submitUrl,
        'cancelUrl' => $cancelUrl,
        'name' => $paymentData['name'],
    ]) ?>

</div>


<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/* @var $submitUrl string */
/* @var $cancelUrl string */
/* @var $paymentModel \frontend\modules\admin\models\forms\EditPaymentMethodForm; */

$formData = $paymentModel->getMethodFormData();

?>

<div class="m-subheader ">
    <div class="d-flex align-items-center">
        <div class="mr-auto">
            <h3 class="m-subheader__title">
                <?= Yii::t('admin', 'settings.payments_edit_paypal') ?>
            </h3>
        </div>
    </div>
</div>

<div class="m-content">

    <div class="sommerce-settings__well">
        <div class="row align-items-center">
            <div class="col-md-3 text-center">
                <img src="<?= $formData['icon'] ?>" alt="" class="img-fluid">
            </div>
            <div class="col-md-9">
                <?= $this->render('_rules', ['method' => $paymentModel->method,]);?>
            </div>
        </div>
    </div>

    <form id="paypalSettingsForm" action="<?= $submitUrl ?>" method="post" role="form">
        <?= Html::beginForm(); ?>

        <?php foreach (ArrayHelper::getValue($formData, 'form_fields') as $formField): ?>
            <div class="form-group">
                <label for="<?= $formField['id'] ?>">
                    <?= $formField['label'] ?>
                </label>
                <input type="text" class="form-control" id="<?= $formField['id'] ?>" placeholder="<?= $formField['placeholder'] ?>" name="<?= $formField['name'] ?>" value="<?= $formField['value'] ?>">
            </div>
        <?php endforeach; ?>

        <div class="form-check">
            <label class="form-check-label">
                <input type="hidden" name="PaymentsForm[details][test_mode]" value="0">
                <input type="checkbox" class="form-check-input" name="PaymentsForm[details][test_mode]" value="1" <?= ArrayHelper::getValue($paymentModel, 'details.test_mode',1) ? 'checked' : '' ?>>
                <?= Yii::t('admin', 'settings.payments_paypal_test_mode') ?>
            </label>
        </div>
        <hr>

        <button type="submit" class="btn btn-success">
                <?= Yii::t('admin', 'settings.payments_save_method') ?>
        </button>
        <a href="<?= $cancelUrl ?>" class="btn btn-secondary">
                <?= Yii::t('admin', 'settings.payments_cancel_method') ?>
        </a>

        <?= Html::endForm(); ?>
    </form>

</div>


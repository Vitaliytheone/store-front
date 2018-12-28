<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/* @var $submitUrl string */
/* @var $cancelUrl string */
/* @var $icon string */
/* @var $description string */
/* @var $formData array */
/* @var $method string from column method_name */
/* @var $paymentModel \sommerce\modules\admin\models\forms\EditPaymentMethodForm; */

$this->context->addModule('adminPayments');
?>

<div class="m-subheader ">
    <div class="d-flex align-items-center">
        <div class="mr-auto">
            <h3 class="m-subheader__title">
                <?= $paymentModel->name ?>
            </h3>
        </div>
    </div>
</div>

<div class="m-content">

    <div class="sommerce-settings__well">
        <div class="row align-items-center">
            <div class="col-md-3 text-center">
                <img src="<?= $icon ?>" alt="" class="img-fluid">
            </div>
            <div class="col-md-9">
                <?= $description ?>
            </div>
        </div>
    </div>

    <form id="editSettingsForm" action="<?= $submitUrl ?>" method="post" role="form">
        <?= Html::beginForm(); ?>
        <div id="editPaymentMethodOptions">
        <?php foreach ($formData as $formField): ?>

            <?= $formField ?>

        <?php endforeach; ?>
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


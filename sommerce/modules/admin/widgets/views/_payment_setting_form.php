<?php

/** @var $paymentData array */
/** @var $widget Widget */
/** @var $submitUrl string */
/** @var $cancelUrl string */
/** @var $name string */

use yii\helpers\Html;
use yii\base\Widget;

?>

<form id="editSettingsForm" action="<?= $submitUrl ?>" method="post" role="form">
    <?= Html::beginForm(); ?>
    <div id="editPaymentMethodOptions">
        <div class="form-group">
            <?= Html::label(Yii::t('admin', 'settings.payments_edit_method_name'), 'edit-name') ?>
            <?= Html::input('text', 'pay-name', $name, ['id' => 'edit-name', 'class' => 'form-control', 'required' => true]); ?>
        </div>

        <?php foreach ($paymentData as $formField): ?>

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

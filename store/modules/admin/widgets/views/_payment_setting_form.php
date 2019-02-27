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

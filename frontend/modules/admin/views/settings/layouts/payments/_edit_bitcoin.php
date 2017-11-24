<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/* @var $submitUrl string */
/* @var $cancelUrl string */
/* @var $paymentModel \frontend\modules\admin\models\forms\EditPaymentMethodForm; */

$imgPath = '/img/';

?>

<!-- BEGIN: Subheader -->
<div class="m-subheader ">
    <div class="d-flex align-items-center">
        <div class="mr-auto">
            <h3 class="m-subheader__title">
                <?= Yii::t('admin', 'settings.payments_edit_bitcoin') ?>
            </h3>
        </div>
    </div>
</div>
<!-- END: Subheader -->
<div class="m-content">

    <div class="sommerce-settings__well">
        <div class="row align-items-center">
            <div class="col-md-3 text-center">
                <img src="<?= $imgPath ?>/bitcoin.png" alt="" class="img-fluid">
            </div>
            <div class="col-md-9">
                <ol>
                    <li>
                        <?= Yii::t('admin', 'settings.payments_bitcoin_guide_1', [
                                'myselium_url' => '<a href="https://gear.mycelium.com/" target="_blank">Mycelium Gear</a>'
                        ]) ?>

                    </li>
                    <li>
                        <?= Yii::t('admin', 'settings.payments_bitcoin_guide_2', [
                                'mycelium_gateway_url' => '<a href="https://admin.gear.mycelium.com/gateways/new" target="_blank">https://admin.gear.mycelium.com/gateways/new</a>'
                        ]) ?>
                        <ul>
                            <li>
                                <?= Yii::t('admin', 'settings.payments_bitcoin_guide_2_1', [
                                        'callback_url' => '<code>http://twig.perfectpanel.net/bitcoin</code>',
                                ]) ?>
                            </li>
                            <li>
                                <?= Yii::t('admin', 'settings.payments_bitcoin_guide_2_2', [
                                    'redirect_url' => '<code>http://twig.perfectpanel.net/addfunds</code>',
                                ]) ?>
                            </li>
                            <li>
                                <?= Yii::t('admin', 'settings.payments_bitcoin_guide_2_3', [
                                    'back_url' => '<code>http://twig.perfectpanel.net/addfunds</code>',
                                ]) ?>
                            </li>
                            <li>
                                <i><?= Yii::t('admin', 'settings.payments_bitcoin_guide_2_4') ?></i>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <?= Yii::t('admin', 'settings.payments_bitcoin_guide_3') ?>
                    </li>
                </ol>
            </div>
        </div>
    </div>
    <form id="checkoutSettingsForm" action="<?= $submitUrl ?>"
          method="post" role="form">
        <?= Html::beginForm(); ?>
        <div class="form-group">
            <label for="bitcoin_api_gateway_id">
                <?= Yii::t('admin', 'settings.payments_bitcoin_gateway_id') ?>
            </label>
            <input type="text" class="form-control" id="bitcoin_api_gateway_id" placeholder="" name="PaymentsForm[details][api_gateway_id]"
                   value="<?= ArrayHelper::getValue($paymentModel, 'details.api_gateway_id', '') ?>">
        </div>
        <div class="form-group">
            <label for="bitcoin_geteway_secret">
                <?= Yii::t('admin', 'settings.payments_bitcoin_gateway_secret') ?>
            </label>
            <input type="text" class="form-control" id="bitcoin_geteway_secret" placeholder="" name="PaymentsForm[details][api_gateway_secret]"
                   value="<?= ArrayHelper::getValue($paymentModel, 'details.api_gateway_secret', '') ?>">
        </div>
        <hr>
        <button type="submit" class="btn btn-success m-btn--air">
            <?= Yii::t('admin', 'settings.payments_save_method') ?>
        </button>
        <a href="<?= $cancelUrl ?>" class="btn btn-secondary">
            <?= Yii::t('admin', 'settings.payments_cancel_method') ?>
        </a>
        <?= Html::endForm(); ?>
    </form>
</div>

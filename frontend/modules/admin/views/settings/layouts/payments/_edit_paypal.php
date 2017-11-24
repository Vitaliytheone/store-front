<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use frontend\helpers\UiHelper;

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
                <?= Yii::t('admin', 'settings.payments_edit_paypal') ?>
            </h3>
        </div>
    </div>
</div>
<!-- END: Subheader -->
<div class="m-content">
    <div class="sommerce-settings__well">
        <div class="row align-items-center">
            <div class="col-md-3 text-center">
                <img src="<?= $imgPath ?>/paypal.png" alt="" class="img-fluid">
            </div>
            <div class="col-md-9">
                <ol>
                    <li>
                        <?= Yii::t('admin', 'settings.payments_paypal_guide_1') ?>
                    </li>
                    <li>
                        <?= Yii::t('admin', 'settings.payments_paypal_guide_2',[
                            'api_credentials_url' => '<a href="https://www.paypal.com/us/cgi-bin/webscr?cmd=_get-api-signature&generic-flow=true" target="_blank">API Credentials</a>'
                        ]) ?>
                    </li>
                    <li>
                        <?= Yii::t('admin', 'settings.payments_paypal_guide_3') ?>
                    </li>
                </ol>
            </div>
        </div>
    </div>




    <form id="paypalSettingsForm" action="<?= $submitUrl ?>" method="post" role="form">
        <?= Html::beginForm(); ?>
        <div class="form-group">
            <label for="paypal_api_username">
                <?= Yii::t('admin', 'settings.payments_paypal_username') ?>
            </label>
            <input type="text" class="form-control" id="paypal_api_username" placeholder="" name="PaymentsForm[details][api_username]"
                   value="<?= ArrayHelper::getValue($paymentModel, 'details.api_username', '') ?>">
        </div>
        <div class="form-group">
            <label for="paypal_api_password">
                <?= Yii::t('admin', 'settings.payments_paypal_password') ?>
            </label>
            <input type="password" class="form-control" id="paypal_api_password" placeholder="" name="PaymentsForm[details][api_password]"
                   value="<?= ArrayHelper::getValue($paymentModel, 'details.api_password', '') ?>">
        </div>
        <div class="form-group">
            <label for="paypal_api_signature">
                <?= Yii::t('admin', 'settings.payments_paypal_signature') ?>
            </label>
            <input type="text" class="form-control" id="paypal_api_signature" placeholder="" name="PaymentsForm[details][api_signature]"
                   value="<?= ArrayHelper::getValue($paymentModel, 'details.api_signature', '') ?>">

        </div>
        <div class="form-check">
            <label class="form-check-label">
                <input type="hidden" name="PaymentsForm[details][test_mode]" value="0">
                <input type="checkbox" class="form-check-input" name="PaymentsForm[details][test_mode]" value="1"
                <?= UiHelper::toggleString(ArrayHelper::getValue($paymentModel, 'details.test_mode', 1), 'checked') ?>>
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


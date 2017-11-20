<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use frontend\helpers\Ui;

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
                <?= Yii::t('admin', 'settings.section_payments_edit_2checkout_title') ?>
            </h3>
        </div>
    </div>
</div>
<!-- END: Subheader -->
<div class="m-content">

    <div class="sommerce-settings__well">
        <div class="row align-items-center">
            <div class="col-md-3 text-center">
                <img src="<?= $imgPath ?>2checkout.png" alt="" class="img-fluid">
            </div>
            <div class="col-md-9">
                <ol>
                    <li>
                        <?= Yii::t('admin', 'settings.section_payments_edit_2checkout_guide_text_1') ?>
                    </li>
                    <li>
                        <?= Yii::t('admin', 'settings.section_payments_edit_2checkout_guide_text_2') ?>
                    </li>
                    <li>
                        <?= Yii::t('admin', 'settings.section_payments_edit_2checkout_guide_text_3') ?>
                    </li>
                </ol>
            </div>
        </div>
    </div>

    <form id="checkoutSettingsForm" action="<?= $submitUrl ?>"
          method="post" role="form">
        <?= Html::beginForm(); ?>
        <div class="form-group">
            <label for="credit_card_number">
                <?= Yii::t('admin', 'settings.section_payments_edit_2checkout_account_number_title') ?>
            </label>
            <input type="text" class="form-control" id="credit_card_number"  placeholder="" name="PaymentsForm[details][account_number]"
                   value="<?= ArrayHelper::getValue($paymentModel, 'details.account_number', '') ?>">
        </div>
        <div class="form-group">
            <label for="credit_card_word">
                <?= Yii::t('admin', 'settings.section_payments_edit_2checkout_secret_word_title') ?>
            </label>
            <input type="password" class="form-control" id="credit_card_word" placeholder="" name="PaymentsForm[details][secret_word]"
                   value="<?= ArrayHelper::getValue($paymentModel, 'details.secret_word', '') ?>">
        </div>
        <div class="form-check">
            <label class="form-check-label">
                <input type="hidden" name="PaymentsForm[details][test_mode]" value="0">
                <input type="checkbox" class="form-check-input" name="PaymentsForm[details][test_mode]" value="1"
                    <?= Ui::toggleString(ArrayHelper::getValue($paymentModel, 'details.test_mode', 1), 'checked') ?>>
                <?= Yii::t('admin', 'settings.section_payments_edit_2checkout_test_mode_label') ?>
            </label>
        </div>
        <hr>
        <button type="submit" class="btn btn-success m-btn--air">
            <?= Yii::t('admin', 'settings.section_payments_edit_2checkout_button_save_title') ?>
        </button>
        <a href="<?= $cancelUrl ?>" class="btn btn-secondary">
            <?= Yii::t('admin', 'settings.section_payments_edit_2checkout_button_cancel_title') ?>
        </a>
        <?= Html::endForm(); ?>
    </form>

</div>

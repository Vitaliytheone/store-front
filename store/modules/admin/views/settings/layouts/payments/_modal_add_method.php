<?php

use my\components\ActiveForm;
use store\modules\admin\models\forms\AddPaymentMethodForm;

/** @var $availableMethods array */

$model = new AddPaymentMethodForm();
?>

<div class="modal fade add-method-modal" id="addPaymentMethodModal" data-backdrop="static" tabindex="-1" role="dialog"  aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-loader hidden"></div>
            <div class="modal-header">
                <h5 class="modal-title"><?= Yii::t('admin', 'settings.payments_modal_title') ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php $form = ActiveForm::begin([
                'id' => 'addPaymentMethodForm',
                'fieldClass' => 'yii\bootstrap\ActiveField',
                'fieldConfig' => [
                    'template' => "{label}\n{input}",
                    'labelOptions' => ['class' => 'form'],
                ],
            ]); ?>
                <div class="modal-body">
                    <?= $form->errorSummary($model, [
                        'id' => 'addPaymentMethodError'
                    ]); ?>
                    <div class="form-group m-form__group">
                        <?= $form->field($model, 'method')->dropDownList($availableMethods) ?>
                    </div>
                </div>
                <div class="modal-footer justify-content-start">
                    <button type="submit" class="btn btn-primary m-btn--air btn_submit" id="addPaymentMethodButton"><?= Yii::t('admin', 'settings.payments_modal_save') ?></button>
                    <button type="button" class="btn btn-secondary m-btn--air" data-dismiss="modal"><?= Yii::t('admin', 'settings.payments_modal_cancel') ?></button>
                </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
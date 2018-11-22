<?php
/* @var $this yii\web\View */
/* @var $model superadmin\models\forms\EditInvoiceCreditForm */
/* @var $form my\components\ActiveForm */

use superadmin\models\forms\AddInvoiceEarningsForm;
use my\components\ActiveForm;
use yii\bootstrap\Html;
use my\helpers\Url;

$model = new AddInvoiceEarningsForm();
?>
<div class="modal fade" id="addEarningsModal" tabindex="-1" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= Yii::t('app/superadmin', 'invoices.add_earnings.header')?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <?php $form = ActiveForm::begin([
                'id' => 'addEarningsForm',
                'action' => Url::toRoute('/invoices/edit-credit'),
                'options' => [
                    'class' => "form",
                ],
                'fieldClass' => 'yii\bootstrap\ActiveField',
                'fieldConfig' => [
                    'template' => "{label}\n{input}",
                ],
            ]); ?>

            <div class="modal-body">
                <?= $form->errorSummary($model, [
                    'id' => 'addEarningsError'
                ]); ?>

                <?= $form->field($model, 'credit') ?>

                <?= $form->field($model, 'memo') ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= Yii::t('app/superadmin', 'invoices.add_earnings.close_btn') ?></button>
                <?= Html::submitButton(Yii::t('app/superadmin', 'invoices.add_earnings.submit_btn'), [
                    'class' => 'btn btn-outline btn-primary',
                    'name' => 'edit-credit-button',
                    'id' => 'addEarningsButton'
                ]) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
<?php
    /* @var $this yii\web\View */
    /* @var $model my\modules\superadmin\models\forms\EditInvoiceForm */
    /* @var $form my\components\ActiveForm */

    use my\modules\superadmin\models\forms\EditInvoiceForm;
    use my\components\ActiveForm;
    use yii\bootstrap\Html;
    use my\helpers\Url;

    $model = new EditInvoiceForm();
?>
<div class="modal fade" id="editInvoiceModal" tabindex="-1" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= Yii::t('app/superadmin', 'invoices.edit_invoice.header')?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <?php $form = ActiveForm::begin([
                'id' => 'editInvoiceForm',
                'action' => Url::toRoute('/invoices/edit'),
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
                    'id' => 'addPaymentError'
                ]); ?>

                <?= $form->field($model, 'total') ?>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= Yii::t('app/superadmin', 'invoices.edit_invoice.close_btn') ?></button>
                <?= Html::submitButton(Yii::t('app/superadmin', 'invoices.edit_invoice.submit'), [
                    'class' => 'btn btn-outline btn-primary',
                    'name' => 'edit-invoice-button',
                    'id' => 'editInvoiceButton'
                ]) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
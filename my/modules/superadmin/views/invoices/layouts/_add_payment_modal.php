<?php
    /* @var $this yii\web\View */
    /* @var $model superadmin\models\forms\AddInvoicePaymentForm */
    /* @var $form my\components\ActiveForm */

    use superadmin\models\forms\AddInvoicePaymentForm;
    use my\components\ActiveForm;
    use yii\bootstrap\Html;
    use my\helpers\Url;

    $model = new AddInvoicePaymentForm();
?>
<div class="modal fade" id="addPaymentModal" tabindex="-1" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= Yii::t('app/superadmin', 'invoices.add_payment.header')?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <?php $form = ActiveForm::begin([
                'id' => 'addPaymentForm',
                'action' => Url::toRoute('/invoices/add-payment'),
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

                <?= $form->field($model, 'method')->dropDownList($model->getMethods()) ?>

                <?= $form->field($model, 'memo') ?>

                <?= $form->field($model, 'fee') ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= Yii::t('app/superadmin', 'invoices.add_payment.close_btn') ?></button>
                <?= Html::submitButton(Yii::t('app/superadmin', 'invoices.add_payment.add_payment_submit'), [
                    'class' => 'btn btn-outline btn-primary',
                    'name' => 'add-payment-button',
                    'id' => 'addPaymentButton'
                ]) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
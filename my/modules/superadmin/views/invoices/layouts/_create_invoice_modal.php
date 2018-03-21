<?php
    /* @var $this yii\web\View */
    /* @var $model my\modules\superadmin\models\forms\CreateInvoiceForm */
    /* @var $form my\components\ActiveForm */

    use my\modules\superadmin\models\forms\CreateInvoiceForm;
    use my\components\ActiveForm;
    use yii\bootstrap\Html;
    use my\helpers\Url;

    $model = new CreateInvoiceForm();
?>
<div class="modal fade" id="createInvoiceModal" tabindex="-1" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= Yii::t('app/superadmin', 'invoices.create_invoice.header')?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <?php $form = ActiveForm::begin([
                'id' => 'createInvoiceForm',
                'action' => Url::toRoute('/invoices/create'),
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

                <div class="form-group field-createinvoiceform-customer_id">
                    <label class="control-label" for="createinvoiceform-customer_id"><?= $model->getAttributeLabel('customer_id')?></label>
                    <select id="createinvoiceform-customer_id" class="form-control selectpicker" name="CreateInvoiceForm[customer_id]" data-live-search="true">
                        <?php foreach ($model->getCustomers() as $customer) : ?>
                            <option data-tokens="<?= $customer->email ?>" value="<?= $customer->id ?>" <?= ($customer->id == $model->customer_id ? 'selected' : '') ?>>
                                <?= $customer->email ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <?= $form->field($model, 'total') ?>

                <?= $form->field($model, 'description')->textarea() ?>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= Yii::t('app/superadmin', 'invoices.create_invoice.close_btn') ?></button>
                <?= Html::submitButton(Yii::t('app/superadmin', 'invoices.create_invoice.submit'), [
                    'class' => 'btn btn-outline btn-primary',
                    'name' => 'create-invoice-button',
                    'id' => 'createInvoiceButton'
                ]) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
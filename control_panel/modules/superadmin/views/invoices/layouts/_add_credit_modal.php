<?php
    /* @var $this yii\web\View */
    /* @var $model superadmin\models\forms\EditInvoiceCreditForm */
    /* @var $form control_panel\components\ActiveForm */

    use superadmin\models\forms\EditInvoiceCreditForm;
    use control_panel\components\ActiveForm;
    use yii\bootstrap\Html;
    use control_panel\helpers\Url;

    $model = new EditInvoiceCreditForm();
?>
<div class="modal fade" id="editCreditModal" tabindex="-1" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= Yii::t('app/superadmin', 'invoices.add_credit.header')?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <?php $form = ActiveForm::begin([
                'id' => 'editCreditForm',
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
                    'id' => 'editCreditError'
                ]); ?>

                <?= $form->field($model, 'credit') ?>

                <?= $form->field($model, 'memo') ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= Yii::t('app/superadmin', 'invoices.add_credit.close_btn') ?></button>
                <?= Html::submitButton(Yii::t('app/superadmin', 'invoices.add_credit.submit_btn'), [
                    'class' => 'btn btn-outline btn-primary',
                    'name' => 'edit-credit-button',
                    'id' => 'editCreditButton'
                ]) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
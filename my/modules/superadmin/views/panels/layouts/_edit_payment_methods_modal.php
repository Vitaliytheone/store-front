<?php
    use common\components\ActiveForm;
    use superadmin\models\forms\EditPanelPaymentMethodsForm;
    use yii\bootstrap\Html;

    /* @var $this yii\web\View */
    /* @var $model EditPanelPaymentMethodsForm */

    $model = new EditPanelPaymentMethodsForm();
?>
<div class="modal fade" id="editPaymentMethodsModal" tabindex="-1" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= Yii::t('app/superadmin', 'panels.edit.payment_methods.header') ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <?php $form = ActiveForm::begin([
                'id' => 'editPaymentMethodsForm',
                'options' => [
                    'class' => "form",
                ],
                'fieldClass' => 'yii\bootstrap\ActiveField',
                'fieldConfig' => [
                    'template' => "{label}\n{input}",
                ],
            ]); ?>
            <div class="modal-body max-height-400">
                <?= $form->errorSummary($model, [
                    'id' => 'editPaymentMethodsError'
                ]); ?>

                <div id="editPaymentMethodsContainer"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= Yii::t('app/superadmin', 'panels.edit.payment_methods.close') ?></button>
                <?= Html::submitButton(Yii::t('app/superadmin', 'panels.edit.payment_methods.save'), [
                    'class' => 'btn btn-outline btn-primary',
                    'name' => 'change-domain-button',
                    'id' => 'editPaymentMethodsButton'
                ]) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
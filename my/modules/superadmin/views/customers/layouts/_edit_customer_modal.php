<?php
    /* @var $this yii\web\View */
    /* @var $model my\modules\superadmin\models\forms\EditCustomerForm */
    /* @var $form my\components\ActiveForm */

    use my\components\ActiveForm;
    use my\modules\superadmin\models\forms\EditCustomerForm;
    use yii\bootstrap\Html;

    $model = new EditCustomerForm();
?>

<div class="modal fade" id="editCustomerModal" data-backdrop="static" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= Yii::t('app/superadmin', 'customers.edit.title') ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?= Yii::t('app/superadmin', 'customers.modal.close_btn') ?>">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <?php $form = ActiveForm::begin([
                'id' => 'editCustomerForm',
                'fieldClass' => 'yii\bootstrap\ActiveField',
                'fieldConfig' => [
                    'template' => "{label}\n{input}",
                    'labelOptions' => ['class' => 'form'],
                ],
            ]); ?>

            <div class="modal-body">
                <?= $form->errorSummary($model, [
                    'id' => 'editCustomerError'
                ]); ?>
                <div class="form-group">
                    <?= $form->field($model, 'email'); ?>
                </div>
                <div class="form-group">
                <?= $form->field($model, 'referral_status')
                    ->dropDownList(
                            $model->getReferrals(),
                            ['class' => 'form-control', 'id' => 'edit-customer-referral']
                    ) ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal"><?= Yii::t('app/superadmin', 'customers.edit.btn_cancel') ?></button>
                <?= Html::submitButton(Yii::t('app/superadmin', 'customers.edit.btn_save'), [
                    'class' => 'btn btn-primary',
                    'id' => 'editCustomerButton'
                ]) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

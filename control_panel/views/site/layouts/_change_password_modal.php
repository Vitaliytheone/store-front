<?php
    /* @var $this yii\web\View */
    /* @var $model \control_panel\models\forms\ChangePasswordForm */
    /* @var $form \control_panel\components\ActiveForm */

    use yii\bootstrap\Html;
    use control_panel\models\forms\ChangePasswordForm;
    use control_panel\components\ActiveForm;

    $model = new ChangePasswordForm();
?>
<div class="modal fade" id="changePassword" tabindex="-1" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?= Yii::t('app', 'customer.change_password_modal.header')?></h4>
            </div>
            <?php $form = ActiveForm::begin([
                'id' => 'change-password-form',
                'fieldConfig' => [
                    'template' => "{input}",
                ],
            ]); ?>
                <div class="modal-body">
                    <?= $form->errorSummary($model, [
                        'id' => 'changePasswordError'
                    ]); ?>
                    <div class="form-group">
                        <label for=""><?= $model->getAttributeLabel('old_password')?></label>
                        <?= Html::textInput('ChangePasswordForm[old_password]', $model->old_password, [
                            'type' => 'password',
                            'class' => 'form-control',
                        ]) ?>
                    </div>
                    <div class="form-group">
                        <label for=""><?= $model->getAttributeLabel('password')?></label>
                        <?= Html::textInput('ChangePasswordForm[password]', $model->password, [
                            'type' => 'password',
                            'class' => 'form-control',
                        ]) ?>
                    </div>
                    <div class="form-group">
                        <label for=""><?= $model->getAttributeLabel('password_repeat')?></label>
                        <?= Html::textInput('ChangePasswordForm[password_repeat]', $model->password_repeat, [
                            'type' => 'password',
                            'class' => 'form-control',
                        ]) ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?= Yii::t('app', 'customer.change_password_modal.btn_cancel')?></button>
                    <button type="submit" class="btn btn-outline btn-primary" id="changePasswdSubmit"><?= Yii::t('app', 'customer.change_password_modal.btn_submit')?></button>
                </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
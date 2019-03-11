<?php
    /* @var $this yii\web\View */
    /* @var $model \control_panel\models\forms\ChangeEmailForm */
    /* @var $form \control_panel\components\ActiveForm */

    use yii\bootstrap\Html;
    use control_panel\models\forms\ChangeEmailForm;
    use control_panel\components\ActiveForm;

    $model = new ChangeEmailForm();
?>
<div class="modal fade" id="changeEmail" tabindex="-1" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?= Yii::t('app', 'customer.change_email_modal.header')?></h4>
            </div>
            <?php $form = ActiveForm::begin([
                'id' => 'change-email-form',
                'fieldConfig' => [
                    'template' => "{label}{input}",
                ],
            ]); ?>
                <div class="modal-body">

                    <?= $form->errorSummary($model, [
                        'id' => 'changeEmailError'
                    ]); ?>

                    <div class="form-group">
                        <label><?= $model->getAttributeLabel('old_email')?></label>
                        <?= Html::textInput('ChangeEmailForm[old_email]', $model->old_email, [
                            'type' => 'email',
                            'class' => 'form-control',
                            'readonly' => 'readonly'
                        ]) ?>
                    </div>
                    <div class="form-group">
                        <label><?= $model->getAttributeLabel('email')?></label>
                        <?= Html::textInput('ChangeEmailForm[email]', $model->email, [
                            'type' => 'email',
                            'class' => 'form-control',
                            'id' => 'changeEmail_email'
                        ]) ?>
                    </div>
                    <div class="form-group">
                        <label><?= $model->getAttributeLabel('password')?></label>
                        <?= Html::textInput('ChangeEmailForm[password]', $model->password, [
                            'type' => 'password',
                            'class' => 'form-control',
                            'id' => 'changeEmail_password'
                        ]) ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?= Yii::t('app', 'customer.change_email_modal.btn_cancel')?></button>
                    <button type="submit" class="btn btn-outline btn-primary" id="changeEmailSubmit"><?= Yii::t('app', 'customer.change_email_modal.btn_submit')?></button>
                </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
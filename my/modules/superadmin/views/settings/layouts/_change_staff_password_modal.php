<?php
    /* @var $this yii\web\View */
    /* @var $form my\components\ActiveForm */
    /* @var $model \superadmin\models\forms\ChangeStaffPasswordForm */

    use my\helpers\Url;
    use yii\helpers\Html;
    use my\components\ActiveForm;
    use superadmin\models\forms\ChangeStaffPasswordForm;

    $model = new ChangeStaffPasswordForm();
?>
<div class="modal fade" id="changePasswordModal" tabindex="-1" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= Yii::t('app/superadmin', 'staff.change_password.modal_header') ?></h5>
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
            </div>
            <?php $form = ActiveForm::begin([
                'id' => 'changePasswordForm',
                'action' => Url::toRoute('/settings/staff-password'),
                'fieldConfig' => [
                    'template' => "{label}\n{input}",
                ],
            ]); ?>
            <div class="modal-body">

                <?= $form->errorSummary($model, [
                    'id' => 'changePasswordError'
                ]); ?>

                <div class="form-group">
                    <label for=""><?= $model->getAttributeLabel('password') ?></label>
                    <div class="input-group mb-3">
                        <?= Html::textInput('ChangeStaffPasswordForm[password]', '', ['class' => 'form-control password'])?>
                        <div class="input-group-append">
                            <button class="btn btn-secondary random-password" type="button"><?=Yii::t('app/superadmin', 'staff.change_password.modal_generate_btn')?></button>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn  btn-light" data-dismiss="modal"><?= Yii::t('app/superadmin', 'staff.change_password.modal_cancel_btn') ?></button>

                <?= Html::submitButton(Yii::t('app/superadmin', 'staff.change_password.modal_change_password'), [
                    'class' => 'btn btn-outline btn-primary',
                    'name' => 'change-password-button',
                    'id' => 'changePasswordButton'
                ]) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
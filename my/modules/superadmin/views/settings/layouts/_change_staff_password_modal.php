<?php
    /* @var $this yii\web\View */
    /* @var $form my\components\ActiveForm */
    /* @var $model \my\modules\superadmin\models\forms\ChangeStaffPasswordForm */

    use my\helpers\Url;
    use yii\helpers\Html;
    use my\components\ActiveForm;
    use my\modules\superadmin\models\forms\ChangeStaffPasswordForm;

    $model = new ChangeStaffPasswordForm();
?>
<div class="modal fade" id="changePasswordModal" tabindex="-1" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Change password</h4>
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
                    <div class="input-group">
                        <?= Html::textInput('ChangeStaffPasswordForm[password]', '', ['class' => 'form-control password'])?>
                        <span class="input-group-addon">
                            <span class="btn btn-default random-password pointer">
                                <i class="fa fa-random fa-fw" data-toggle="tooltip" data-placement="right" title="Generate password"></i>
                            </span>
                        </span>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>

                <?= Html::submitButton('Change password', [
                    'class' => 'btn btn-outline btn-primary',
                    'name' => 'change-password-button',
                    'id' => 'changePasswordButton'
                ]) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
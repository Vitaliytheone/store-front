<?php
    /* @var $this yii\web\View */
    /* @var $tickets \common\models\panels\Tickets */
    /* @var $ticket \common\models\panels\Tickets */
    /* @var $model \my\models\forms\SetStaffPasswordForm */

    use my\components\ActiveForm;
    use yii\bootstrap\Html;
    use my\models\forms\SetStaffPasswordForm;

    $model = new SetStaffPasswordForm();
?>
<div class="modal fade" id="setStaffPasswordModal" tabindex="-1" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?= Yii::t('app', 'panels.edit_staff_password.header')?></h4>
            </div>
            <?php $form = ActiveForm::begin([
                'id' => 'setStaffPasswordForm',
                'fieldConfig' => [
                    'template' => "{label}{input}",
                ],
                'options' => [
                    'class' => 'form'
                ]
            ]); ?>
                <div class="modal-body">
                    <?= $form->errorSummary($model, [
                        'id' => 'setStaffPasswordError'
                    ]); ?>

                    <?= $form->field($model, 'username')->textInput(['readonly' => 'readonly']) ?>

                    <div class="form-group">
                        <label for=""><?= $model->getAttributeLabel('password') ?></label>
                        <div class="input-group">
                            <?= Html::textInput('SetStaffPasswordForm[password]', '', ['class' => 'form-control password'])?>
                            <span class="input-group-btn random-password">
                                <button class="btn btn-default" type="button" id="staff_edit_gen"><i class="fa fa-random fa-fw" data-toggle="tooltip" data-placement="right" title="Generate password"></i></button>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?= Yii::t('app', 'panels.edit_staff_password.modal_cancel') ?></button>

                    <?= Html::submitButton(Yii::t('app', 'panels.edit_staff_password.modal_submit'), [
                        'class' => 'btn btn-outline btn-primary',
                        'name' => 'set-staff-password-button',
                        'id' => 'setStaffPasswordButton'
                    ]) ?>
                </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
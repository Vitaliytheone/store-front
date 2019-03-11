<?php
    /* @var $this yii\web\View */
    /* @var $model superadmin\models\forms\CustomerPasswordForm */
    /* @var $form control_panel\components\ActiveForm */

    use control_panel\components\ActiveForm;
    use superadmin\models\forms\CustomerPasswordForm;
    use control_panel\helpers\Url;
    use yii\bootstrap\Html;

    $model = new CustomerPasswordForm();
?>

<div class="modal fade" id="setPasswordModal" data-backdrop="static" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= Yii::t('app/superadmin', 'customers.set_password.title') ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <?php $form = ActiveForm::begin([
                'id' => 'setPasswordForm',
                'action' => Url::toRoute('/customers/set-password'),
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
                    'id' => 'setPasswordError'
                ]); ?>

                <div class="form-group">
                    <label for="form-apikey"><?= $model->getAttributeLabel('password') ?></label>
                    <div class="input-group mb-3">
                        <?= Html::textInput('CustomerPasswordForm[password]', '', ['class' => 'form-control password', 'id' => 'copyTarget'])?>
                        <div class="input-group-append">
                            <button class="btn btn-secondary random-password" data-clipboard-target="#copyTarget" type="button"><?= Yii::t('app/superadmin', 'customers.set_password.btn_generate') ?></button>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal"><?= Yii::t('app/superadmin', 'customers.set_password.btn_cancel') ?></button>
                <?= Html::submitButton(Yii::t('app/superadmin', 'customers.set_password.btn_save'), [
                    'class' => 'btn btn-primary',
                    'name' => 'set-password-button',
                    'id' => 'setPasswordButton',
                ]) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

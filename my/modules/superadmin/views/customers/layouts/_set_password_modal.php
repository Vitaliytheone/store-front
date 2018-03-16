<?php
    /* @var $this yii\web\View */
    /* @var $model my\modules\superadmin\models\forms\CustomerPasswordForm */
    /* @var $form my\components\ActiveForm */

    use my\components\ActiveForm;
    use my\modules\superadmin\models\forms\CustomerPasswordForm;
    use my\helpers\Url;
    use yii\bootstrap\Html;

    $model = new CustomerPasswordForm();
?>

<div class="modal fade" id="setPasswordModal" tabindex="-1" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Set password</h4>
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
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
                    <label for=""><?= $model->getAttributeLabel('password') ?></label>
                    <div class="input-group">
                        <?= Html::textInput('CustomerPasswordForm[password]', '', ['class' => 'form-control password'])?>
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
                <?= Html::submitButton('Set password', [
                    'class' => 'btn btn-outline btn-primary',
                    'name' => 'set-password-button',
                    'id' => 'setPasswordButton'
                ]) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
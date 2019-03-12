<?php
    /* @var $this yii\web\View */
    /* @var $form control_panel\components\ActiveForm */
    /* @var $model \control_panel\models\forms\ResetPasswordForm */

    use control_panel\components\ActiveForm;
?>
<div class="container">
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <div class="login-panel panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><?= Yii::t('app', 'index.reset_password.header')?></h3>
                </div>
                <div class="panel-body">
                    <?php $form = ActiveForm::begin([
                    'id' => 'reset-password-form',
                    'fieldConfig' => [
                        'template' => "{input}",
                        ],
                    ]); ?>
                        <fieldset>
                            <?= $form->errorSummary($model); ?>

                            <div class="form-group">
                                <?= $form->field($model, 'password')->passwordInput(['class' => 'form-control', 'placeholder' => $model->getAttributeLabel('password')]) ?>
                            </div>

                            <div class="form-group">
                                <?= $form->field($model, 'password_repeat')->passwordInput(['class' => 'form-control', 'placeholder' => $model->getAttributeLabel('password_repeat')]) ?>
                            </div>

                            <button type="submit" class="btn btn-outline btn-primary btn-lg btn-block"><?= Yii::t('app', 'index.reset_password.btn_submit')?></button>
                        </fieldset>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>

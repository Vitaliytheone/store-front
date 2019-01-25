<?php
    /* @var $this yii\web\View */
    /* @var $form my\components\ActiveForm */
    /* @var $model \superadmin\models\forms\PasswordUpdateForm */

    use yii\helpers\Html;
    use my\components\ActiveForm;

?>
<br>
<div class="container">
    <div class="row justify-content-center">
        <div class="card col-md-5">
            <div class="card-body">
                <?php $form = ActiveForm::begin([
                        'id' => 'password-update-form',
                        'fieldConfig' => [
                            'template' => "{label}\n{input}",
                            'labelOptions' => ['class' => 'control-label'],
                        ],
                ]); ?>
                <?= $form->successMessage() ?>

                <?= $form->errorSummary($model); ?>

                <?= $form->field($model, 'current_password')->passwordInput(['autofocus' => true]) ?>

                <?= $form->field($model, 'password')->passwordInput() ?>

                <?= $form->field($model, 'password_repeat')->passwordInput() ?>

                <?= Html::submitButton('Change password', ['class' => 'btn btn-primary', 'name' => 'save-button']) ?>
                <?php ActiveForm::end(); ?>

            </div>
        </div>
    </div>
</div>
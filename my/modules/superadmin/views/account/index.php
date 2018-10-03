<?php
    /* @var $this yii\web\View */
    /* @var $form my\components\ActiveForm */
    /* @var $model \my\modules\superadmin\models\forms\PasswordUpdateForm */

    use yii\helpers\Html;
    use my\components\ActiveForm;

?>

<div class="row">
    <div class="col-sm-12 col-md-10 offset-md-1 col-lg-8 offset-lg-2">
        <div class="card">
            <div class="card-block">
                <br>
                <div class="input-group">
                    <div class="col-lg-12">
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
                <br>
            </div>
        </div>
    </div>
</div>
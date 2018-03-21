<?php

/* @var $this yii\web\View */
/* @var $form my\components\ActiveForm */
/* @var $model my\modules\superadmin\models\forms\LoginForm */

use yii\helpers\Html;
use my\components\ActiveForm;
use himiklab\yii2\recaptcha\ReCaptcha;

$this->title = 'Admin';

?>


<div class="admin-form-wrapper">
    <?php $form = ActiveForm::begin([
        'id' => 'login-form',
        'options' => [
            'class' => 'admin_authorization'
        ]
    ]); ?>

    <?= $form->errorSummary($model); ?>

    <?= $form->field($model, 'username')->textInput(['autofocus' => true]) ?>

    <?= $form->field($model, 'password')->passwordInput() ?>

    <?php if ($model->isCheckCaptcha()) : ?>
        <?= ReCaptcha::widget(['name' => 're_captcha']) ?>
        <br />
    <?php endif; ?>

    <div class="form-group">
        <?= Html::submitButton('Login', ['class' => 'btn btn-default', 'name' => 'login-button']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

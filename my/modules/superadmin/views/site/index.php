<?php

/* @var $this yii\web\View */
/* @var $form my\components\ActiveForm */
/* @var $model superadmin\models\forms\LoginForm */

use yii\helpers\Html;
use my\components\ActiveForm;
use himiklab\yii2\recaptcha\ReCaptcha;

$this->title = Yii::t('app/superadmin', 'site.title');

?>
 <div class="row justify-content-center">
     <div class="card col-md-5">
         <div class="card-body">
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
                <?= $form->field($model, 're_captcha')->widget(ReCaptcha::class) ?>
                <br />
            <?php endif; ?>

            <div class="form-group">
                <?= Html::submitButton('Login', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
            </div>

            <?php ActiveForm::end(); ?>
         </div>
     </div>
 </div>

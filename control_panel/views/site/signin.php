<?php

    /* @var $this yii\web\View */
    /* @var $form control_panel\components\ActiveForm */
    /* @var $model \control_panel\models\forms\LoginForm */

    use himiklab\yii2\recaptcha\ReCaptcha;
    use control_panel\components\ActiveForm;
    use yii\bootstrap\Html;

?><div class="container">
    <div class="row">
      <div class="col-md-4 col-md-offset-4">
        <div class="login-panel panel panel-default">
          <div class="panel-heading">
            <h3 class="panel-title"><?= Yii::t('app', 'index.signin.header')?></h3>
          </div>
          <div class="panel-body">

            <?php $form = ActiveForm::begin([
                'id' => 'login-form',
                'fieldConfig' => [
                    'template' => "{input}",
                ],
            ]); ?>

              <fieldset>
                  <?= $form->errorSummary($model); ?>

                <div class="form-group">
                  <?= $form->field($model, 'username')->textInput([
                      'placeholder' => $model->getAttributeLabel('username'),
                      'autofocus' => true,
                      'class' => 'form-control'
                  ]) ?>
                </div>
                <div class="form-group">
                    <?= $form->field($model, 'password')->passwordInput([
                        'class' => 'form-control',
                        'placeholder' => $model->getAttributeLabel('password')
                    ]) ?>
                </div>

                <div class="row">
                  <div class="col-sm-12 signIn-forgotPassword">
                      <a href="/forgot " ><?= Yii::t('app', 'index.signin.forgot_password') ?></a>
                  </div>
                </div>

                <?php if ($model->isCheckCaptcha()) : ?>
                  <?= ReCaptcha::widget([
                        'model' => $model,
                        'attribute' => 're_captcha',
                    ]) ?>
                  <br />
                <?php endif; ?>

                <button type="submit" class="btn btn-outline btn-primary btn-lg btn-block"><?= Yii::t('app', 'index.signin.btn_submit')?></button>
              </fieldset>
            <?php ActiveForm::end(); ?>
          </div>
        </div>
        <div class="text-center">
          <span><?= Yii::t('app', 'index.signin.sugnup_hint')?></span>
          <a href="/signup" ><?= Yii::t('app', 'index.signin.link_sugnup') ?></a>
        </div>
      </div>
    </div>
  </div>

























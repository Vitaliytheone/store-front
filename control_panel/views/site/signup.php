<?php
    /* @var $this yii\web\View */
    /* @var $form control_panel\components\ActiveForm */
    /* @var $model \control_panel\models\forms\SignupForm */

    use yii\helpers\Html;
    use control_panel\components\ActiveForm;
    use himiklab\yii2\recaptcha\ReCaptcha;
?>
<div class="container">
  <div class="row">
    <div class="col-md-4 col-md-offset-4">
      <div class="login-panel panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title"><?= Yii::t('app', 'index.signup.header')?></h3>
        </div>
        <div class="panel-body">

          <?php $form = ActiveForm::begin([
              'id' => 'signup-form',
              'fieldConfig' => [
                  'template' => "{input}",
              ],
          ]); ?>
            <fieldset>

                <?= $form->errorSummary($model); ?>

              <div class="form-group">
                <?= $form->field($model, 'first_name')->textInput([
                    'autofocus' => true,
                    'class' => 'form-control',
                    'placeholder' => $model->getAttributeLabel('first_name')
                ]) ?>
              </div>
              <div class="form-group">
                <?= $form->field($model, 'last_name')->textInput(['class' => 'form-control', 'placeholder' => $model->getAttributeLabel('last_name')]) ?>
              </div>
              <div class="form-group">
                <?= $form->field($model, 'email')->textInput(['class' => 'form-control', 'placeholder' => $model->getAttributeLabel('email')]) ?>
              </div>
              <div class="form-group">
                <?= $form->field($model, 'password')->passwordInput(['class' => 'form-control', 'placeholder' => $model->getAttributeLabel('password')]) ?>
              </div>
              <div class="form-group">
                <?= $form->field($model, 'password_confirm')->passwordInput(['class' => 'form-control', 'placeholder' => $model->getAttributeLabel('password_confirm')]) ?>
              </div>

              <?= ReCaptcha::widget([
                  'model' => $model,
                  'attribute' => 're_captcha',
              ]) ?>

              <div class="checkbox">
                <label>
                    <?= Html::checkbox('SignupForm[terms]', $model->terms, [
                        'id' => 'signupform-terms',
                    ]) ?>
                    <?= Yii::t('app', 'index.signup.terms_of_service_hint', [
                        'link' => Html::a(Yii::t('app', 'index.signup.terms_of_service'), 'javascript:void(0)', [
                            'onclick' => "this.blur(); window.open('http://perfectpanel.com/terms#tos',null,'toolbar=0,left=20,top=20,resizable=1,scrollbars=1,width=800,height=600');"
                        ])
                    ])?>
                </label>
              </div>

              <button type="submit" class="btn btn-outline btn-success btn-lg btn-block">
                  <?= Yii::t('app', 'index.signup.btn_submit')?>
              </button>
            </fieldset>
          <?php ActiveForm::end(); ?>
        </div>
      </div>
      <div class="text-center">
        <span><?= Yii::t('app', 'index.signup.signin_hint')?></span>
        <a href="/signin" ><?= Yii::t('app', 'index.signup.link_signin')?></a>
      </div>
    </div>
  </div>
</div>























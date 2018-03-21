<?php
    /* @var $this yii\web\View */
    /* @var $form my\components\ActiveForm */
    /* @var $model \my\models\forms\RestoreForm */
    /* @var $success boolean */
    /* @var $successMessage string */

    use my\components\ActiveForm;
    use himiklab\yii2\recaptcha\ReCaptcha;
    use yii\bootstrap\Html;
?>
<div class="container">
    <div class="row">
        <div class="col-md-4 col-md-offset-4">

            <div class="login-panel panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><?= Yii::t('app', 'index.restore.header')?></h3>
                </div>
                <div class="panel-body">
                    <?php $form = ActiveForm::begin([
                    'id' => 'restore-form',
                    'fieldConfig' => [
                    'template' => "{input}",
                    ],
                    ]); ?>

                        <?php if (!empty($success)) : ?>
                            <div class="alert alert-info">
                                <?= $successMessage ?>
                            </div>
                        <?php endif; ?>

                        <fieldset>
                            <?= $form->errorSummary($model); ?>

                            <div class="form-group">
                                <?= $form->field($model, 'email')->textInput(['autofocus' => true, 'class' => 'form-control', 'placeholder' => $model->getAttributeLabel('email')]) ?>
                            </div>

                            <div class="form-group">
                                <?= ReCaptcha::widget([
                                    'model' => $model,
                                    'attribute' => 're_captcha',
                                ]) ?>
                            </div>

                            <button type="submit" class="btn btn-outline btn-primary btn-lg btn-block"><?= Yii::t('app', 'index.restore.btn_submit')?></button>
                        </fieldset>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
            <div class="text-center">
                <a href="/signin"><?= Yii::t('app', 'index.restore.back_to_login')?></a>
            </div>
        </div>
    </div>
</div>

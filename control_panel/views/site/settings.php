<?php
    /* @var $this yii\web\View */
    /* @var $form \control_panel\components\ActiveForm */
    /* @var $model \control_panel\models\forms\SettingsForm */

    use control_panel\components\ActiveForm;
?>
<div class="row">
    <div class="col-lg-12">
        <h2 class="page-header"><?= Yii::t('app', 'customer.settings.header')?></small></h2>
    </div>
</div>
<div class="row">
    <div class="col-lg-8">
        <div class="panel panel-default">
            <div class="panel-body">
                <?php $form = ActiveForm::begin([
                'id' => 'login-form',
                'fieldConfig' => [
                  'template' => "{label}{input}",
                ],
                ]); ?>
                    <?= $form->errorSummary($model); ?>

                    <div class="form-group">
                        <?= $form->field($model, 'first_name')->textInput(['autofocus' => true, 'class' => 'form-control', 'placeholder' => $model->getAttributeLabel('first_name')]) ?>
                    </div>
                    <div class="form-group">
                        <?= $form->field($model, 'last_name')->textInput(['class' => 'form-control', 'placeholder' => $model->getAttributeLabel('last_name')]) ?>
                    </div>
                    <div class="form-group">
                        <label><?=$model->getAttributeLabel('email')?></label>
                        <p class="form-control-static"><?= $model->email ?> <a href="#" id="changeEmailBtn"><?= Yii::t('app', 'customer.settings.link_change_email')?></a></p>
                    </div>
                    <div class="form-group">
                        <label><?= $model->getAttributeLabel('password')?></label>
                        <p class="form-control-static"><a href="#" id="changePasswordBtn"><?= Yii::t('app', 'customer.settings.link_change_password')?></a></p>
                    </div>
                    <div class="form-group">
                        <?= $form->field($model, 'timezone')->dropDownList($model->getTimezones(), ['class' => 'form-control']) ?>
                    </div>
                    <button type="submit" class="btn btn-outline btn-primary"><?= Yii::t('app', 'customer.settings.btn_submit')?></button>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>

<?= $this->render('layouts/_change_email_modal') ?>
<?= $this->render('layouts/_change_password_modal') ?>

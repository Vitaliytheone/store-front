<?php
    /* @var $this yii\web\View */
    /* @var $model superadmin\models\forms\CreateNotificationEmailForm */
    /* @var $form control_panel\components\ActiveForm */

    use control_panel\helpers\Url;
    use yii\bootstrap\Html;
    use control_panel\components\ActiveForm;

?>
<div class="container">
    <div class="row">
        <div class="col-md-2">
            <div class="list-group list-group__custom">
                <?= $this->render('layouts/_menu'); ?>
            </div>
        </div>
        <div class="col-lg-8">
            <h3><?= Yii::t('app/superadmin', 'settings.create_email.header') ?></h3>
            <div class="form-gr">
                    <?php $form = ActiveForm::begin([
                        'id' => 'createEmailForm',
                        'options' => [
                            'class' => "form",
                        ],
                        'fieldClass' => 'yii\bootstrap\ActiveField',
                        'fieldConfig' => [
                            'template' => "{label}\n{input}",
                        ],
                    ]); ?>

                        <?= $form->errorSummary($model, [
                            'id' => 'createEmailError'
                        ]); ?>

                        <?= $form->field($model, 'subject') ?>

                        <?= $form->field($model, 'code') ?>

                        <?= $form->field($model, 'message')->textarea(['rows' => 5]) ?>

                        <?= $form->field($model, 'enabled')->checkbox() ?>

                    <?= Html::submitButton(Yii::t('app/superadmin', 'settings.create_email.save'), [
                            'class' => 'btn btn-outline btn-primary',
                            'name' => 'create-email-button',
                            'id' => 'createEmailButton'
                        ]) ?>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>

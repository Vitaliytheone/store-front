<?php
    /* @var $this yii\web\View */
    /* @var $model my\modules\superadmin\models\forms\CreateNotificationEmailForm */
    /* @var $form my\components\ActiveForm */

    use my\helpers\Url;
    use yii\bootstrap\Html;
    use my\components\ActiveForm;

?>
<div class="container">
    <div class="row">
        <div class="col-md-2">
            <div class="list-group list-group__custom">
                <a href="<?=Url::toRoute('/settings')?>" class="list-group-item list-group-item-action"><span class="fa fa-credit-card"></span> <?=Yii::t('app/superadmin', 'pages.settings.menu_payments')?></a>
                <a href="<?=Url::toRoute('/settings/staff')?>" class="list-group-item list-group-item-action"><span class="fa fa-user"></span> <?=Yii::t('app/superadmin', 'pages.settings.menu_staff')?></a>
                <a href="<?=Url::toRoute('/settings/email')?>" class="list-group-item list-group-item-action"><span class="fa fa-envelope-o"></span> <?=Yii::t('app/superadmin', 'pages.settings.menu_email')?></a>
                <a href="<?=Url::toRoute('/settings/plan')?>" class="list-group-item list-group-item-action"><span class="fa fa-list-alt"></span> <?=Yii::t('app/superadmin', 'pages.settings.menu_plan')?></a>
                <a href="<?=Url::toRoute('/settings/content')?>" class="list-group-item list-group-item-action active"><span class="fa fa-file-text-o"></span> <?=Yii::t('app/superadmin', 'pages.settings.content')?></a>
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

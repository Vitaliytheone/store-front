<?php
    /* @var $this yii\web\View */
    /* @var $model superadmin\models\forms\CreateNotificationEmailForm */
    /* @var $form my\components\ActiveForm */

    use my\helpers\Url;
    use yii\bootstrap\Html;
    use my\components\ActiveForm;

?>
<div class="container mt-3">
    <div class="row">
        <div class="col-lg-2 offset-lg-1">
            <ul class="nav nav-pills flex-column mb-3">
                <li class="nav-item">
                    <?= Html::a('Payments', Url::toRoute('/settings'), ['class' => 'nav-link'])?>
                </li>
                <li class="nav-item">
                    <?= Html::a('Staff', Url::toRoute('/settings/staff'), ['class' => 'nav-link'])?>
                </li>
                <li class="nav-item">
                    <?= Html::a('Email', Url::toRoute('/settings/email'), ['class' => 'nav-link bg-faded'])?>
                </li>
            </ul>
        </div>
        <div class="col-lg-8">
            <h3><?= Yii::t('app/superadmin', 'settings.edit_email.header') ?></h3>
            <div class="card">
                <div class="card-block">
                    <?php $form = ActiveForm::begin([
                        'id' => 'editEmailForm',
                        'options' => [
                            'class' => "form",
                        ],
                        'fieldClass' => 'yii\bootstrap\ActiveField',
                        'fieldConfig' => [
                            'template' => "{label}\n{input}",
                        ],
                    ]); ?>

                    <?= $form->errorSummary($model, [
                        'id' => 'editEmailError'
                    ]); ?>

                    <?= $form->field($model, 'subject') ?>

                    <?= $form->field($model, 'message')->textarea(['rows' => 5]) ?>

                    <?= $form->field($model, 'code')->textInput([
                        'disabled' => 'disabled'
                    ]) ?>

                    <?= Html::submitButton(Yii::t('app/superadmin', 'settings.edit_email.save_changes'), [
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
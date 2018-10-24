<?php
/* @var $this yii\web\View */
/* @var $model my\modules\superadmin\models\forms\CreateNotificationEmailForm */
/* @var $form my\components\ActiveForm */

use my\helpers\Url;
use yii\bootstrap\Html;
use my\components\ActiveForm;
use my\modules\superadmin\models\forms\EditNotificationEmailForm;

$model = new EditNotificationEmailForm();
?>

<div class="modal fade" id="editEmail" data-backdrop="static" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= Yii::t('app/superadmin', 'settings.create_email.header') ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

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

            <div class="modal-body">
                <?= $form->errorSummary($model, [
                    'id' => 'createEmailError'
                ]); ?>

                <?= $form->field($model, 'subject') ?>

                <?= $form->field($model, 'code') ?>

                <?= $form->field($model, 'message')->textarea(['rows' => 5]) ?>

                <?= $form->field($model, 'enabled')->checkbox() ?>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn  btn-light" data-dismiss="modal"><?= Yii::t('app/superadmin', 'content.edit.modal_cancel_btn')?></button>
                <?= Html::submitButton(Yii::t('app/superadmin', 'settings.create_email.save'), [
                    'class' => 'btn btn-primary',
                    'name' => 'create-email-button',
                    'id' => 'createEmailButton'
                ]) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

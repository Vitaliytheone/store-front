<?php
    /* @var $this yii\web\View */
    /* @var $form \common\components\ActiveForm */

    use common\components\ActiveForm;
    use yii\bootstrap\Html;
    use sommerce\modules\admin\components\Url;

    $model = new \sommerce\modules\admin\models\forms\EditAdminEmailForm();
?>
<div class="modal fade"  id="createAdminEmailModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <?php $form = ActiveForm::begin([
            'id' => 'createAdminEmailForm',
            'action' => Url::toRoute('/settings/create-email'),
            'options' => [
                'class' => "modal-content m-form",
            ],
            'fieldClass' => 'yii\bootstrap\ActiveField',
            'fieldConfig' => [
                'template' => "{label}\n{input}",
            ],
        ]); ?>
            <div class="modal-header">
                <h5 class="modal-title"><?= Yii::t('admin', 'settings.emails_m_create_header') ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?= $form->errorSummary($model, [
                    'id' => 'createAdminEmailError'
                ]); ?>

                <div class="form-group m-form__group">
                    <?= $form->field($model, 'email') ?>
                </div>
            </div>
            <div class="modal-footer">
                <?= Html::submitButton(Yii::t('admin', 'settings.emails_m_add'), [
                    'class' => 'btn btn-secondary',
                    'name' => 'create-admin-email-button',
                    'id' => 'createAdminEmailButton'
                ]) ?>
                <button type="button" class="btn btn-primary" data-dismiss="modal"><?= Yii::t('admin', 'settings.emails_m_cancel') ?></button>
            </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
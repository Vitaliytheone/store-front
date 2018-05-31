<?php
    /* @var $this \yii\web\View */
    /* @var $model \sommerce\modules\admin\models\forms\SendTestNotificationForm */

    use sommerce\modules\admin\models\forms\SendTestNotificationForm;
    use common\components\ActiveForm;
    use yii\bootstrap\Html;

    $model = new SendTestNotificationForm();
?>
<div class="modal fade" tabindex="-1" role="dialog" id="sendTestNotificationModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= Yii::t('admin', 'settings.send_test_m_header') ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php $form = ActiveForm::begin([
                'id' => 'sendTestNotificationForm',
                'fieldConfig' => [
                    'template' => "{label}{input}",
                ],
            ]); ?>
                <div class="modal-body">
                    <?= $form->errorSummary($model, [
                        'id' => 'sendTestNotificationError'
                    ]); ?>
                    <div class="form-group m-form__group">
                        <?= $form->field($model, 'admin_email_id')->dropDownList($model->getAdminEmails(), [
                            'class' => 'form-control m-input m-input--square'
                        ])?>
                    </div>

                </div>
                <div class="modal-footer">
                    <?= Html::submitButton(Yii::t('admin', 'settings.send_test_m_confirm'), [
                        'class' => 'btn btn-primary',
                        'name' => 'send-test-notification-button',
                        'id' => 'sendTestNotificationButton'
                    ]) ?>

                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= Yii::t('admin', 'settings.send_test_m_cancel') ?></button>
                </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
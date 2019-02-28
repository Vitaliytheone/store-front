<?php
    /* @var $this yii\web\View */
    /* @var $tickets \common\models\sommerces\Tickets */
    /* @var $ticket \common\models\sommerces\Tickets */
    /* @var $model \control_panel\models\forms\CreateTicketForm */

    use control_panel\components\ActiveForm;
    use common\components\cdn\providers\widgets\UploadcareUploadWidget;

?>
<div class="modal fade" id="submitTicket" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?= Yii::t('app', 'support.create_ticket.header') ?></h4>
            </div>
            <div class="modal-body">
                <?php $form = ActiveForm::begin([
                    'id' => 'support-form',
                    'action' => '/create-ticket',
                    'fieldConfig' => [
                        'template' => "{label}{input}",
                    ],
                ]); ?>
                <?= $form->errorSummary($model, [
                    'id' => 'createTicketError'
                ]); ?>

                <div class="form-group">
                    <?= $form->field($model, 'subject')->textInput(['required' => true, 'maxlength' => 300, 'class' => 'form-control']) ?>
                </div>
                <div class="form-group">
                    <?= $form->field($model, 'message')->textArea(['required' => true, 'maxlength' => 1000, 'class' => 'form-control', 'rows' => 7]) ?>
                </div>
                <div class="form-group">
                    <label><?= Yii::t('app', 'support.view_form.attachment') ?></label>
                    <br>
                    <?= UploadcareUploadWidget::widget(); ?>
                </div>
                <div class="text-right">
                    <button type="submit" class="btn btn-outline btn-primary">
                        <?= Yii::t('app', 'support.create_ticket.btn_submit') ?>
                    </button>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
<?php

use my\helpers\Url;
use my\components\ActiveForm;
use my\modules\superadmin\models\forms\CreateMessageForm;

$model = new CreateMessageForm();
?>

<!----------------- Edit message ----------------->
<div class="modal fade" id="edit-message-modal" data-backdrop="static" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <?php $form = ActiveForm::begin([
                'id' => 'edit-message-form',
                'action' => Url::toRoute(['/tickets/edit-message']),
            ]); ?>
            <div class="modal-header">
                <h5 class="modal-title"><?= Yii::t('app/superadmin', 'tickets.edit_message') ?></h5>
            </div>
            <div class="modal-body text-center">
                <?= $form->errorSummary($model, [
                    'id' => 'createTicketError'
                ]); ?>
                <textarea id="edit-message-content" name="message" rows="5" class="form-control"></textarea>
            </div>
            <div class="modal-footer">
                <button id="cancel-edit" type="button" class="btn btn-lg btn-light" data-dismiss="modal"><?= Yii::t('app/superadmin', 'tickets.modal.cancel') ?></button>
                <button id="modal-save-edit" type="button" class="btn btn-lg btn-primary"><?= Yii::t('app/superadmin', 'tickets.modal.save') ?></button>
            </div>
            <input hidden id="edit-message" name="messageId" >
            <input hidden id="edit-message-ticketId" name="ticketId" />
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

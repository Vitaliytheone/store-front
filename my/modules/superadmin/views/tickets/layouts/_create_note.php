<?php

use my\modules\superadmin\models\forms\TicketNoteForm;
use my\components\ActiveForm;
use yii\helpers\Html;

$model = new TicketNoteForm();
?>

<div class="modal fade" id="edit-notes" data-backdrop="static" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit notes</h5>
            </div>
            <?php $form = ActiveForm::begin([
                'id' => 'createNoteForm',
                'fieldClass' => 'yii\bootstrap\ActiveField',
                'fieldConfig' => [
                    'template' => "{label}\n{input}",
                    'labelOptions' => ['class' => 'form'],
                ],
            ]); ?>
            <div class="modal-body text-center">
                <?= $form->field($model, 'note')->label(false)->textarea(['class' => 'note_content']) ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-lg btn-light" data-dismiss="modal">Cancel</button>
                <?= Html::submitButton('Save changes', [
                    'class' => 'btn btn-primary',
                    'id' => 'createNoteButton'
                ]) ?>
            </div>
        </div>
    </div>
</div>

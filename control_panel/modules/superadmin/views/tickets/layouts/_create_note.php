<?php

use superadmin\models\forms\TicketNoteForm;
use control_panel\components\ActiveForm;
use yii\helpers\Html;

$model = new TicketNoteForm();
?>

<div class="modal fade" id="createNotesModal" data-backdrop="static" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= Yii::t('app/superadmin', 'tickets.create_node.title') ?></h5>
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
                <?= $form->errorSummary($model, [
                    'id' => 'createNoteError'
                ]); ?>

                <?= $form->field($model, 'note')->label(false)->textarea(['style' => 'height:105px', 'class' => 'form-control']) ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-lg btn-light" data-dismiss="modal"><?= Yii::t('app/superadmin', 'tickets.create_node.cancel_btn') ?></button>
                <?= Html::submitButton(Yii::t('app/superadmin', 'tickets.create_node.save_btn'), [
                    'class' => 'btn btn-lg btn-primary',
                    'id' => 'createNoteButton'
                ]) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

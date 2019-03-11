<?php
/* @var $this yii\web\View */
/* @var $model \superadmin\models\forms\CreateMessageForm */
/* @var $ticket \common\models\sommerces\Tickets */

use common\components\cdn\providers\widgets\UploadcareUploadWidget;
use control_panel\components\ActiveForm;
use control_panel\helpers\Url;
use yii\bootstrap\Html;

?>

<?php $form = ActiveForm::begin([
    'id' => 'create-message-form',
    'fieldConfig' => [
        'template' => "{label}\n{input}",
        'labelOptions' => ['class' => 'control-label'],
    ],
]); ?>

<?= $form->successMessage() ?>

<?= $form->errorSummary($model); ?>


    <div class="ticket-message mb-3">
        <?= $form->field($model, 'message')->textarea([
            'rows' => '7',
            'class' => 'form-control',
            'id' => 'createmessageform-message',
            'required' => true,
        ])->label(false) ?>
    </div>

    <div class="row">
        <div class="col-md-8 d-md-flex align-items-center">
            <?= Html::submitButton(Yii::t('app/superadmin', 'tickets.btn.submit_reply'), ['class' => 'btn btn-primary', 'name' => 'save-button']) ?>
            <div class="form-control-file ml-md-3 btn-uploadcare">
                <?= UploadcareUploadWidget::widget(); ?>
            </div>
        </div>
        <div class="col-md-4 text-md-right">
            <?= Html::a(Yii::t('app/superadmin', 'tickets.btn.mark_unread'), Url::toRoute(['/tickets/mark-unread', 'id' => $ticket->id]), [
                'class' => 'btn btn-link btn-no-line',
                'style' => 'padding: .375rem 0rem;'
            ]) ?>
        </div>
    </div>

<?php ActiveForm::end(); ?>
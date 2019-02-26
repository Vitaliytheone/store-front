<?php
/* @var $ticket \common\models\panels\Tickets */

use my\models\forms\CreateMessageForm;
use my\components\ActiveForm;
use common\components\cdn\providers\widgets\UploadcareUploadWidget;

$model = new CreateMessageForm();

?>

<?php $form = ActiveForm::begin([
    'id' => 'ticketForm',
    'action' => '/message/' . $ticket->id,
    'fieldConfig' => [
        'template' => "{input}",
    ],
]); ?>

<?= $form->errorSummary($model, [
    'id' => 'ticketMessageError'
]); ?>

    <div class="form-group">
        <?= $form->field($model, 'message')->textarea([
            'rows' => 5,
            'id' => 'message',
            'required' => true,
        ]) ?>
    </div>
    <div class="form-group">
        <label><?= Yii::t('app', 'support.view_form.attachment') ?></label>
        <br>
        <?= UploadcareUploadWidget::widget(); ?>
    </div>
    <div class="text-right">
        <button type="submit" class="btn btn-outline btn-primary"><?= Yii::t('app', 'support.view_form.btn_submit') ?></button>
    </div>
<?php ActiveForm::end(); ?>
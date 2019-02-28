<?php
    /* @var $ticket \common\models\sommerces\Tickets */

    use control_panel\models\forms\CreateMessageForm;
    use control_panel\components\ActiveForm;

    $model = new CreateMessageForm();
?>

<?php $form = ActiveForm::begin([
    'id' => 'ticketForm',
    'action' => "/message/". $ticket->id,
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
        ])?>
    </div>
    <div class="text-right">
        <button type="submit" class="btn btn-outline btn-primary"><?= Yii::t('app', 'support.view_form.btn_submit')?></button>
    </div>
<?php ActiveForm::end(); ?>
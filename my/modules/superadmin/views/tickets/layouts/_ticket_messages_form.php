<?php
    /* @var $this yii\web\View */
    /* @var $model \my\modules\superadmin\models\forms\CreateMessageForm */
    /* @var $ticket \common\models\panels\Tickets */

    use common\models\panels\Tickets;
    use my\helpers\Url;
    use my\components\ActiveForm;
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

<?= $form->field($model, 'message')->textarea([
    'rows' => 5
]) ?>

<div class="row justify-content-between">
    <div>
        <?= Html::submitButton(Yii::t('app/superadmin', 'tickets.btn.submit_reply'), ['class' => 'btn btn-primary', 'name' => 'save-button']) ?>
    </div>

    <div>
        <?= Html::a(Yii::t('app/superadmin', 'tickets.btn.mark_unread'), Url::toRoute(['/tickets/mark-unread', 'id' => $ticket->id]),['class' => 'btn btn-primary']) ?>
        <?php if (Tickets::STATUS_IN_PROGRESS == $ticket->status) : ?>
            <?= Html::tag('span', Yii::t('app/superadmin', 'tickets.btn.in_progress'), ['class' => 'btn btn-primary active', 'disabled' => 'disabled']) ?>
        <?php else : ?>
            <?= Html::a(Yii::t('app/superadmin', 'tickets.btn.in_progress'), Url::toRoute(['/tickets/change-status', 'id' => $ticket->id, 'status' => Tickets::STATUS_IN_PROGRESS]),['class' => 'btn btn-primary']) ?>
        <?php endif; ?>
        <?php if (Tickets::STATUS_SOLVED == $ticket->status) : ?>
            <?= Html::tag('span', Yii::t('app/superadmin', 'tickets.btn.solved'), ['class' => 'btn btn-primary active', 'disabled' => 'disabled']) ?>
        <?php else : ?>
            <?= Html::a(Yii::t('app/superadmin', 'tickets.btn.solved'), Url::toRoute(['/tickets/change-status', 'id' => $ticket->id, 'status' => Tickets::STATUS_SOLVED]),['class' => 'btn btn-primary']) ?>
        <?php endif; ?>
        <?php if (Tickets::STATUS_CLOSED == $ticket->status) : ?>
            <?= Html::tag('span', Yii::t('app/superadmin', 'tickets.btn.closed'), ['class' => 'btn btn-primary active', 'disabled' => 'disabled']) ?>
        <?php else : ?>
            <?= Html::a(Yii::t('app/superadmin', 'tickets.btn.closed'), Url::toRoute(['/tickets/change-status', 'id' => $ticket->id, 'status' => Tickets::STATUS_CLOSED]),['class' => 'btn btn-primary']) ?>
        <?php endif; ?>
    </div>
</div>

<?php ActiveForm::end(); ?>
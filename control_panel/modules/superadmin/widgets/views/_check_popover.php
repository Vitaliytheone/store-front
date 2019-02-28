<?php
/* @var $message \common\models\sommerces\TicketMessages */

use yii\helpers\Html;
use control_panel\helpers\Url;
?>

<div class='checkPopover'>
    <?= Html::a( Yii::t('app/superadmin', 'tickets.modal.delete') ,
        Url::toRoute(['/tickets/delete-message']), [
            'data-method' => 'POST',
            'data-params' => [
                'messageId' => $message->id,
                'ticketId' => $message->ticket_id,
            ],
            'id' => 'delete-message',
            'class' => 'btn btn-sm btn-primary'
        ]) ?>
    <a href='#' class='btn btn-sm btn-default'><?= Yii::t('app/superadmin', 'tickets.modal.cancel') ?></a>
</div>

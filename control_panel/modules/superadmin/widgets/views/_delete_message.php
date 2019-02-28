<?php

/* @var $message \common\models\sommerces\TicketMessages */

use yii\helpers\Html;
?>

<?= Html::a( Yii::t('app/superadmin', 'tickets.modal.delete') , '#', [
        'class' => 'ticket-message__card-link delete-link',
        'tabindex' => '0',
        'data-toggle' => 'popover',
        'data-trigger' => 'focus',
        'data-content' => $this->render('_check_popover', ['message' => $message]),
]) ?>

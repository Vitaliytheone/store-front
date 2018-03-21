<?php
    /* @var $this yii\web\View */
    /* @var $ticketMessages \common\models\panels\TicketMessages */
    /* @var $ticket \common\models\panels\Tickets */
    /* @var $model \my\modules\superadmin\models\forms\CreateMessageForm */
?>

<div class="container">
    <div class="container-heading">
        <h3 class="container-title"><?= htmlspecialchars($ticket->subject)?></h3>
    </div>
    <div class="container-body">
        <?= $this->render('layouts/_ticket_messages_list', [
            'ticketMessages' => $ticketMessages
        ])?>

        <hr>

        <?= $this->render('layouts/_ticket_messages_form', [
            'ticket' => $ticket,
            'model' => $model
        ])?>
    </div>
</div>
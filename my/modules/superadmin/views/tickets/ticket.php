<?php
    /* @var $this yii\web\View */
    /* @var $ticketMessages array */
    /* @var $ticket \common\models\panels\Tickets */
    /* @var $model \my\modules\superadmin\models\forms\CreateMessageForm */
    /* @var $admins array */
    /* @var $statuses array */
    /* @var $ticketMessagesSearch my\modules\superadmin\models\search\TicketMessagesSearch */
    /* @var $stores  array */
    /* @var $ssl  array */
    /* @var $panels  array */
    /* @var $childPanels  array */
    /* @var $domains  array */

    use my\components\ActiveForm;
    use my\helpers\Url;
    use yii\helpers\Html;

    $this->context->addModule('superadminTicketsEditController');
?>

<div class="container">
    <div class="row">
        <div class="col-md-12 mb-0">
            <h4 class="mb-0 ticket-title">
                <?= htmlspecialchars($ticket->subject)?>
                <div class="ticket-id">#<?= $ticket->id ?></div>
            </h4>
        </div>
    </div>


    <div class="row">

        <div class="col-lg-8 order-lg-1 order-2">

            <?= $this->render('layouts/_ticket_messages_form', [
                'ticket' => $ticket,
                'model' => $model
            ])?>

            <div class="ticket-message__block">
                <?= $this->render('layouts/_ticket_messages_list', [
                    'ticketMessages' => $ticketMessages,
                    'admins' => $admins,
                    'ticketMessagesSearch' => $ticketMessagesSearch
                ])?>
            </div>

        </div>

        <div class="col-lg-4 order-lg-2 order-1">
            <?= $this->render('layouts/_ticket_customer_info', [
                'ticket' => $ticket,
                'admins' => $admins,
                'stores' => $stores,
                'ssl' => $ssl,
                'childPanels' => $childPanels,
                'panels' => $panels,
                'statuses' => $statuses,
                'domains' => $domains
            ])?>
        </div>
    </div>
</div>

<?php $this->beginBlock('modals'); ?>
<?= $this->render('layouts/_edit_message_modal.php')?>
<?= $this->endBlock();?>
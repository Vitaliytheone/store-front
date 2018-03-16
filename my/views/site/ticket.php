<?php
    /* @var $this yii\web\View */
    /* @var $ticketMessages \common\models\panels\TicketMessages */
    /* @var $ticket \common\models\panels\Tickets */
    /* @var $message \common\models\panels\TicketMessages */
    /* @var $clear */
    /* @var $showForm */

    use yii\helpers\Url;
?>
<div class="form-group" id="htmlText" data-action="<?= Url::toRoute("/ticket/ ". $ticket->id . '?clear=1')?>">
    <ul class="chat">
        <?php foreach ($ticketMessages as $message) : ?>
            <?php if ($message->cid != 0): ?>
                <li class="left clearfix">
                <div class="chat-body clearfix text-right">
                    <div class="header">
                        <strong class="primary-font"><?= $message->customer->getFullName() ?></strong>
                    <small class="text-muted"><i class="fa fa-clock-o fa-fw"></i>  <?= $message->getFormattedDate('date') ?></small>
                    </div>
                    <p class=""><?= nl2br(htmlspecialchars($message->message)) ?></p>
                </div>
                </li>
            <?php else: ?>
                <li class="right clearfix">
                    <div class="chat-body clearfix">
                        <div class="header">
                            <strong class="primary-font"><?= $message->admin->getFullName() ?></strong>
                            <small class="text-muted"><i class="fa fa-clock-o fa-fw"></i>  <?= $message->getFormattedDate('date') ?></small>
                        </div>
                        <p class=""><?= nl2br(htmlspecialchars($message->message)) ?></p>
                    </div>
                </li>
            <?php endif ?>
        <?php endforeach ?>
    </ul>
</div>
<?php if ($showForm): ?>
    <?= $this->render('layouts/_ticket_details_form', [
        'ticket' => $ticket
    ])?>
<?php endif ?>
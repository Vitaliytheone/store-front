<?php
/* @var $this yii\web\View */
/* @var $ticketMessages array */
/* @var $message \common\models\panels\TicketMessages */
/* @var $admins array */
/* @var $ticketMessagesSearch superadmin\models\search\TicketMessagesSearch */

use superadmin\helpers\SystemMessages;
use superadmin\widgets\DeleteMessageWidget;
use common\components\cdn\providers\widgets\UploadcareSuperadminWidget;

$i = 0;
?>

<?php foreach ($ticketMessages as $message) : ?>
    <?php if ($message->customer_id != 0): ?>
        <?php $customer = $message->customer; ?>
        <div class="ticket-message__card ticket-message__client">
            <div class="ticket-message__card-header">
                <div class="ticket-message__card-username"><?= $customer->getFullName() ?> </div>
                <div class="ticket-message__card-date"><?= $message->getFormattedDate('created_at') ?></div>
            </div>
            <div class="ticket-message__card-text">
                <?= nl2br(htmlspecialchars($message->message)) ?>
                <div class="ticket-message__card-attach">
                    <?php if (!empty($message->file->details)) {
                        echo UploadcareSuperadminWidget::widget(['files' => $message->file]);
                    } ?>
                </div>
            </div>
        </div>
    <?php else: ?>
        <?php if ($message->is_system != 0) : ?>
            <?= (SystemMessages::getSystemMessageWidget($message->getSystemInfo()))::widget([
                'admin' => $message->admin->getFullName(),
                'data' => $message->getSystemInfo(),
                'date' => $message->getFormattedDate('created_at'),
                'admins' => $admins,
            ]) ?>
        <?php endif; ?>
        <?php if ($message->is_system == 0): ?>
            <div class="ticket-message__card ticket-message__support">
                <div class="ticket-message__card-header">
                    <div class="ticket-message__card-username"><?= $message->admin->getFullName() ?></div>
                    <div class="ticket-message__card-date"><?= $message->getFormattedDate('created_at') ?></div>
                </div>
                <div class="ticket-message__card-text">
                    <?= nl2br(htmlspecialchars($message->message)) ?>
                    <div class="ticket-message__card-attach">
                        <?php if (!empty($message->file->details)) {
                            echo UploadcareSuperadminWidget::widget(['files' => $message->file]);
                        } ?>
                    </div>
                </div>
            <?php if ($ticketMessagesSearch->canEdit($i)):?>
                <div class="ticket-message__card-footer">
                    <ul>
                        <li>
                            <a href="#" data-content="<?= htmlspecialchars($message->message) ?>" data-ticket="<?= $message->ticket_id ?>" data-id="<?= $message->id ?>"  class="ticket-message__card-link open-edit-modal" data-toggle="modal" data-target="#edit-message-modal">
                                <?= Yii::t('app/superadmin', 'tickets.modal.edit') ?>
                            </a>
                        </li>
                        <li>
                            <?= DeleteMessageWidget::widget(['message' => $message]); ?>
                        </li>
                    </ul>
                </div>
            <?php endif ?>
            </div>
        <?php endif ?>
    <?php endif ?>
    <?php $i++;  ?>
<?php endforeach ?>
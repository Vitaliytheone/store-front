<?php
/* @var $this yii\web\View */
/* @var $ticketMessages \common\models\panels\TicketMessages */
/* @var $ticketFiles \common\models\panels\TicketFiles */
/* @var $ticket \common\models\panels\Tickets */
/* @var $message \common\models\panels\TicketMessages */
/* @var $clear */
/* @var $showForm */

/* @var $cdn \common\components\cdn\providers\Uploadcare */

use my\helpers\Url;

?>
    <div class="form-group" id="htmlText" data-action="<?= Url::toRoute("/ticket/ " . $ticket->id . '?clear=1') ?>">
        <ul class="chat">
            <?php foreach ($ticketMessages as $message) : ?>
                <?php if ($message->customer_id != 0): ?>
                    <li class="left clearfix">
                        <div class="chat-body clearfix text-right">
                            <div class="header">
                                <strong class="primary-font"><?= $message->customer->getFullName() ?></strong>
                                <small class="text-muted"><i class="fa fa-clock-o fa-fw"></i> <?= $message->getFormattedDate('created_at') ?>
                                </small>
                            </div>
                            <p class=""><?= nl2br(htmlspecialchars($message->message)) ?></p>

                            <?php // todo widget
                            if (!empty($message->file)) {
                                if (!empty($message->file->details)) {
                                        $files = $message->file->getDetails();
                                    } elseif (!empty($message->file)) {
                                        $files = $cdn->getFiles($message->file->link, true);
                                    }
                                    echo '<div class="attachments-block"><span class="fa fa-paperclip"></span>';

                                    foreach ($files as $file) {
                                        echo ' <a href = "' . $file['link'] . '" target="_blank" class="attachments-file">' . $file['name'] . '</a> ('.$file['size'].')';
                                    }
                                    echo '</div>';
                            } ?>

                        </div>
                    </li>
                <?php else: ?>
                    <li class="right clearfix">
                        <div class="chat-body clearfix">
                            <div class="header">
                                <strong class="primary-font"><?= $message->admin->getFullName() ?></strong>
                                <small class="text-muted"><i class="fa fa-clock-o fa-fw"></i> <?= $message->getFormattedDate('created_at') ?>
                                </small>
                            </div>
                            <p class=""><?= nl2br(htmlspecialchars($message->message)) ?></p>


                            <?php // todo widget
                            if (!empty($message->file)) {
//                                echo '<div class="attachments-block"><span class="fa fa-paperclip"></span>';
//                                $files = $cdn->getFiles($message->file->link, true);
//
//                                foreach ($files as $file) {
//                                    echo ' <a href = "' . $file['link'] . '" target="_blank" class="attachments-file">' . $file['name'] . '</a>';
//                                }
//                                echo '</div>';
                            } ?>

                        </div>
                    </li>
                <?php endif ?>
            <?php endforeach ?>
        </ul>
    </div>

<?php if ($showForm): ?>
    <?= $this->render('layouts/_ticket_details_form', [
        'ticket' => $ticket,
        'cdn' => $cdn,
    ]) ?>
<?php endif ?>
<?php

/* @var $notes array */
/* @var $ticket array */

use my\helpers\Url;
use my\helpers\SpecialCharsHelper;

?>

<div class="tickets-notes__block">
    <div class="tickets-notes mb-3">
        <div class="tickets-notes__title"><?= Yii::t('app/superadmin', 'tickets.notes.title'); ?></div>
        <div class="tickets-notes__body">

            <?php foreach (SpecialCharsHelper::multiPurifier($notes) as $note) : ?>
            <div class="tickets-notes__row">
                <?= nl2br($note->note) ?>
                <div class="tickets-notes__row-edit">
                    <a href="<?= Url::toRoute(['/tickets/edit-note', 'id' => $note->id]) ?>" class="edit-note" data-note="<?= $note->note ?>" data-toggle="modal">
                        <span class="fa fa-pencil"></span>
                    </a>
                </div>
            </div>
            <?php endforeach; ?>

        </div>
        <div class="tickets-notes__add-block">
            <a href="<?= Url::toRoute(['/tickets/create-note', 'id' => $ticket->customer_id]) ?>" class="create-note" data-toggle="modal">
                <div class="tickets-notes__add">
                    <span class="fa fa-plus"></span>
                </div>
            </a>
        </div>
    </div>
</div>

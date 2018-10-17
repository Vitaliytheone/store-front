<?php

/* @var $notes array */

use my\helpers\Url;

?>

<div class="tickets-notes__block">
    <div class="tickets-notes mb-3">
        <div class="tickets-notes__title">Notes</div>
        <div class="tickets-notes__body">

            <?php foreach ($notes as $note) : ?>
            <div class="tickets-notes__row">
                <?= $note->note ?>
                <div class="tickets-notes__row-edit">
                    <a href="<?= Url::toRoute(['/tickets/edit-note']) ?>" class="edit-note" data-target="#edit-notes" data-toggle="modal">
                        <span class="fa fa-pencil"></span>
                    </a>
                </div>
            </div>
            <?php endforeach; ?>

        </div>
        <div class="tickets-notes__add-block">
            <a href="<?= Url::toRoute(['/tickets/create-note']) ?>" class="create-note" data-target="#edit-notes" data-toggle="modal">
                <div class="tickets-notes__add">
                    <span class="fa fa-plus"></span>
                </div>
            </a>
        </div>
    </div>
</div>

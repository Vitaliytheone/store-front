<?php
/* @var $admins  array */
/* @var $data  object */
/* @var $admin  string */
/* @var $date  string */

use common\models\sommerces\Tickets;

?>
<div class="ticket-message__card ticket-message__status ticket-message__card-status">
    <div class="ticket-message__status-header">
        <span class="ticket-message__status-header-name"><?= $admin ?></span>
        <span class="ticket-message__status-header-date"><?= $date ?></span>
    </div>
    <div class="ticket-message__card-text">
        <div><?= Tickets::getStatuses()[(int)$data->from] ?>
            <span class="fa fa-long-arrow-right"></span>
            <?= Tickets::getStatuses()[(int)$data->to] ?>
        </div>
    </div>
</div>

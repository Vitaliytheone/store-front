<?php
/* @var $admins  array */
/* @var $data  object */
/* @var $admin  string */
/* @var $date  string */
?>
<div class="ticket-message__card ticket-message__status ticket-message__card-assignee">
    <div class="ticket-message__status-header">
        <span class="ticket-message__status-header-name"><?= $admin ?></span>
        <span class="ticket-message__status-header-date"><?= $date ?></span>
    </div>
    <div class="ticket-message__card-text">
        <div>
            <?php if (isset($admins[(int)$data->from])) :
                $adminFrom = $admins[(int)$data->from];
            ?>
                &nbsp;<?= $adminFrom['first_name'] . ' ' . $adminFrom['last_name']?>&nbsp;
            <?php else: ?>
                &nbsp;<?=Yii::t('app/superadmin', 'tickets.unassigned')?>&nbsp;
            <?php endif; ?>
            <span class="fa fa-long-arrow-right"></span>
             <?= $admins[(int)$data->to]['first_name'] . ' ' . $admins[(int)$data->to]['last_name'] ?>
        </div>
        <?php if ($data->comment) : ?>
            <div class="ticket-message__assignee-comment"><?= nl2br(htmlspecialchars($data->comment)) ?></div>
        <?php endif; ?>
    </div>
</div>

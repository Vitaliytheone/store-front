<?php
    /* @var $this yii\web\View */
    /* @var $tickets \my\modules\superadmin\models\search\TicketsSearch */
    /* @var $ticket \common\models\panels\Tickets */

    use my\helpers\Url;
    use yii\helpers\Html;
    use yii\widgets\LinkPager;

?>
<table class="table table-border">
    <thead>
    <tr>
        <th><?= Yii::t('app/superadmin', 'tickets.list.column_id')?></th>
        <th><?= Yii::t('app/superadmin', 'tickets.list.column_customer')?></th>
        <th><?= Yii::t('app/superadmin', 'tickets.list.column_subject')?></th>
        <th><?= Yii::t('app/superadmin', 'tickets.list.column_status')?></th>
        <th class="text-nowrap"><?= Yii::t('app/superadmin', 'tickets.list.column_created')?></th>
        <th class="text-nowrap"><?= Yii::t('app/superadmin', 'tickets.list.column_updated')?></th>
    </tr>
    </thead>
    <tbody>
        <?php if (!empty($tickets['models'])) : ?>
            <?php foreach ($tickets['models'] as $ticket) : ?>
                <tr>
                    <td>
                        <?= $ticket->id ?>
                    </td>
                    <td>
                        <a href="<?= Url::toRoute('/customers#' . $ticket->cid); ?>" target="_blank"><?= $ticket->customer_email ?></a>
                    </td>
                    <td>
                        <?php if ($ticket->user) : ?>
                            <b>
                                <?= Html::a(htmlspecialchars($ticket->subject), Url::toRoute(['/tickets/view', 'id' => $ticket->id])) ?>
                            </b>
                        <?php else : ?>
                            <?= Html::a(htmlspecialchars($ticket->subject), Url::toRoute(['/tickets/view', 'id' => $ticket->id])) ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?= $ticket->getStatusName() ?>
                    </td>
                    <td>
                        <span class="text-nowrap">
                            <?= $ticket->getFormattedDate('date', 'php:Y-m-d') ?>
                        </span>
                        <?= $ticket->getFormattedDate('date', 'php:H:i:s') ?>
                    </td>
                    <td>
                        <span class="text-nowrap">
                            <?= $ticket->getFormattedDate('date_update', 'php:Y-m-d') ?>
                        </span>
                        <?= $ticket->getFormattedDate('date_update', 'php:H:i:s') ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>

    </tbody>
</table>

<div class="text-align-center pager">
    <?= LinkPager::widget([
        'pagination' => $tickets['pages'],
    ]); ?>
</div>
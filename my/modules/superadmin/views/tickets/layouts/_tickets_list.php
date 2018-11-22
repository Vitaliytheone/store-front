<?php
    /* @var $this yii\web\View */
    /* @var $tickets \superadmin\models\search\TicketsSearch */
    /* @var $ticket \common\models\panels\Tickets */
    /* @var $superAdmins */
    /* @var $superAdminCount */
    /* @var $filters */
    /* @var $assignee */

    use my\helpers\Url;
    use yii\helpers\Html;
    use yii\widgets\LinkPager;
    use my\helpers\SpecialCharsHelper;

?>
<table class="table table-sm table-custom">
    <thead>
    <tr>
        <th><?= Yii::t('app/superadmin', 'tickets.list.column_id')?></th>
        <th class="table-custom__customer-th"><?= Yii::t('app/superadmin', 'tickets.list.column_customer')?></th>
        <th><?= Yii::t('app/superadmin', 'tickets.list.column_subject')?></th>
        <th><?= Yii::t('app/superadmin', 'tickets.list.column_status')?></th>
        <th>
            <div class="dropdown">
                <button class="btn table-custom__filter-button dropdown-toggle" data-toggle="dropdown">
                    <?= Yii::t('app/superadmin', 'tickets.assignee') ?>
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <a class="dropdown-item <?= (is_numeric($assignee) ? '' : 'active') ?>" href="<?= Url::toRoute(array_merge($filters, ['/tickets', 'assignee' => null])) ?>"><?= Yii::t('app/superadmin', 'tickets.assignee_list.all', ['count' => $superAdminCount]) ?></a>
                    <?php foreach ($superAdmins as $admin) : ?>
                        <?php if (isset($admin['username'])) : ?>
                            <a class="dropdown-item <?= ($admin['assigned_admin_id'] == $assignee ? 'active' : '') ?>" href=" <?= Url::toRoute(array_merge($filters, ['/tickets', 'assignee' => $admin['assigned_admin_id']])) ?>"><?= $admin['username'] ?>(<?= $admin['count'] ?>)</a>
                        <?php endif ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </th>
        <th><?= Yii::t('app/superadmin', 'tickets.list.column_created')?></th>
        <th><?= Yii::t('app/superadmin', 'tickets.list.column_updated')?></th>
    </tr>
    </thead>
    <tbody>
        <?php if (!empty($tickets['models'])) : ?>
            <?php foreach (SpecialCharsHelper::multiPurifier($tickets['models']) as $key => $ticket) : ?>
                <tr>
                    <td>
                        <?= $ticket->id ?>
                    </td>
                    <td>
                        <a href="<?= Url::toRoute(['/customers', 'query' => $ticket->customer_email]); ?>" target="_blank"><?= $ticket->customer_email ?></a>
                    </td>
                    <td>
                        <?php if ($ticket->is_user) : ?>
                            <b>
                                <?= Html::a($ticket->subject, Url::toRoute(['/tickets/view', 'id' => $ticket->id])) ?>
                            </b>
                        <?php else : ?>
                            <?= Html::a($ticket->subject, Url::toRoute(['/tickets/view', 'id' => $ticket->id])) ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?= $ticket->getStatusName() ?>
                    </td>
                    <td>
                        <?= $ticket->assigned_name ?>
                    </td>
                    <td>
                        <span class="text-nowrap">
                            <?= $ticket->getFormattedDate('created_at', 'php:Y-m-d') ?>
                        </span>
                        <?= $ticket->getFormattedDate('created_at', 'php:H:i:s') ?>
                    </td>
                    <td>
                        <span class="text-nowrap">
                            <?= $ticket->getFormattedDate('updated_at', 'php:Y-m-d') ?>
                        </span>
                        <?= $ticket->getFormattedDate('updated_at', 'php:H:i:s') ?>
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
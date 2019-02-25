<?php

/* @var $this yii\web\View */
/* @var $statuses array */

use control_panel\helpers\SpecialCharsHelper;

$this->context->addModule('superadminStatusesController');
?>

<table class="table table-sm table-custom statuses-table dataTable no-footer" id="data-table" role="grid">
    <thead class="">
    <tr role="row">
        <th class="sorting" tabindex="0" aria-controls="data-table" rowspan="1" colspan="1"><?= Yii::t('app/superadmin', 'subscription.list.column_id') ?></th>
        <th class="sorting statuses-table__provider" tabindex="0" aria-controls="data-table" rowspan="1" colspan="1"><?= Yii::t('app/superadmin', 'subscription.list.column_panel') ?></th>
        <th class="sorting" tabindex="0" aria-controls="data-table" rowspan="1" colspan="1"><?= Yii::t('app/superadmin', 'subscription.list.column_all') ?></th>
        <th class="sorting" tabindex="0" aria-controls="data-table" rowspan="1" colspan="1"><?= Yii::t('app/superadmin', 'subscription.list.column_active') ?></th>
        <th class="sorting" tabindex="0" aria-controls="data-table" rowspan="1" colspan="1"><?= Yii::t('app/superadmin', 'subscription.list.column_paused') ?></th>
        <th class="sorting" tabindex="0" aria-controls="data-table" rowspan="1" colspan="1"><?= Yii::t('app/superadmin', 'subscription.list.column_completed') ?></th>
        <th class="sorting" tabindex="0" aria-controls="data-table" rowspan="1" colspan="1"><?= Yii::t('app/superadmin', 'subscription.list.column_expired') ?></th>
        <th class="sorting" tabindex="0" aria-controls="data-table" rowspan="1" colspan="1"><?= Yii::t('app/superadmin', 'subscription.list.column_canceled') ?></th>
        <th class="sorting" tabindex="0" aria-controls="data-table" rowspan="1" colspan="1"><?= Yii::t('app/superadmin', 'subscription.list.column_avg') ?></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach (SpecialCharsHelper::multiPurifier($model['models']) as $id => $subscription) : ?>
        <tr role="row">
            <td><?= $subscription['id'] ?></td>
            <td><?= $subscription['panel'] ?></td>
            <td><?= $subscription['allCount'] ?></td>
            <td><?= $subscription['activeCount'] ?></td>
            <td><?= $subscription['pausedCount'] ?></td>
            <td><?= $subscription['completedCount'] ?></td>
            <td><?= $subscription['expiredCount'] ?></td>
            <td><?= $subscription['canceledCount'] ?></td>
            <td><?= $subscription['avg'] ?></td>
        </tr>
    <?php endforeach; ?>
    <?php if (!empty($model['models'])) : ?>
        <tr role="row">
            <td><strong><?= Yii::t('app/superadmin', 'sender.list.total') ?></strong></td>
            <td></td>
            <td><?= $model['totals']['all'] ?></td>
            <td><?= $model['totals']['active'] ?></td>
            <td><?= $model['totals']['paused'] ?></td>
            <td><?= $model['totals']['completed'] ?></td>
            <td><?= $model['totals']['expired'] ?></td>
            <td><?= $model['totals']['canceled'] ?></td>
            <td><?= $model['totals']['avg'] ?></td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>
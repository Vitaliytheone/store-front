<?php

/* @var $this yii\web\View */
/* @var $statuses array */

use my\helpers\SpecialCharsHelper;

$this->context->addModule('superadminStatusesController');
?>

<table class="table table-sm table-custom statuses-table dataTable no-footer" id="data-table" role="grid">
    <thead class="">
    <tr role="row">
        <th class="sorting" tabindex="0" aria-controls="data-table" rowspan="1" colspan="1"><?= Yii::t('app/superadmin', 'statuses.list.column_id') ?></th>
        <th class="sorting statuses-table__provider"  tabindex="0" aria-controls="data-table" rowspan="1" colspan="1"><?= Yii::t('app/superadmin', 'statuses.list.column_provider') ?></th>
        <th class="sorting" tabindex="0" aria-controls="data-table" rowspan="1" colspan="1"><?= Yii::t('app/superadmin', 'statuses.list.column_all_orders') ?></th>
        <th class="sorting" tabindex="0" aria-controls="data-table" rowspan="1" colspan="1"><?= Yii::t('app/superadmin', 'statuses.list.column_orders') ?></th>
        <th class="sorting" tabindex="0" aria-controls="data-table" rowspan="1" colspan="1"><?= Yii::t('app/superadmin', 'statuses.list.column_requests') ?></th>
        <th class="sorting" tabindex="0" aria-controls="data-table" rowspan="1" colspan="1"><?= Yii::t('app/superadmin', 'statuses.list.column_good') ?></th>
        <th class="sorting" tabindex="0" aria-controls="data-table" rowspan="1" colspan="1"><?= Yii::t('app/superadmin', 'statuses.list.column_status_error') ?></th>
        <th class="sorting" tabindex="0" aria-controls="data-table" rowspan="1" colspan="1"><?= Yii::t('app/superadmin', 'statuses.list.column_curl_error') ?></th>
        <th class="sorting" tabindex="0" aria-controls="data-table" rowspan="1" colspan="1"><?= Yii::t('app/superadmin', 'statuses.list.column_min') ?></th>
        <th class="sorting" tabindex="0" aria-controls="data-table" rowspan="1" colspan="1"><?= Yii::t('app/superadmin', 'statuses.list.column_max') ?></th>
        <th class="sorting" tabindex="0" aria-controls="data-table" rowspan="1" colspan="1"><?= Yii::t('app/superadmin', 'statuses.list.column_avg') ?></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach (SpecialCharsHelper::multiPurifier($statuses) as $id => $status) : ?>
        <tr role="row">
            <td><?= $id ?></td>
            <td><?= $status['provider'] ?></td>
            <td><?= $status['all_orders'] ?></td>
            <td><?= $status['unique'] ?></td>
            <td><?= $status['requests'] ?></td>
            <td><?= $status['good'] ?></td>
            <td><?= $status['status_error'] ?></td>
            <td><?= $status['curl_error'] ?></td>
            <td><?= $status['min'] ?></td>
            <td><?= $status['max'] ?></td>
            <td><?= $status['avg'] ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php

/* @var $this yii\web\View */
/* @var $statuses array */

use my\helpers\SpecialCharsHelper;

$this->context->addModule('superadminStatusesController');
?>

<table class="table table-sm table-custom statuses-table dataTable no-footer" id="data-table" role="grid">
    <thead class="">
    <tr role="row">
        <th class="sorting statuses-table__provider"  tabindex="0" aria-controls="data-table" rowspan="1" colspan="1"><?= Yii::t('app/superadmin', 'statuses.list.column_provider') ?></th>
        <th class="sorting" tabindex="0" aria-controls="data-table" rowspan="1" colspan="1"><?= Yii::t('app/superadmin', 'statuses.list.column_send_method') ?></th>
        <th class="sorting" tabindex="0" aria-controls="data-table" rowspan="1" colspan="1"><?= Yii::t('app/superadmin', 'statuses.list.column_all') ?></th>
        <th class="sorting" tabindex="0" aria-controls="data-table" rowspan="1" colspan="1"><?= Yii::t('app/superadmin', 'statuses.list.column_good') ?></th>
        <th class="sorting" tabindex="0" aria-controls="data-table" rowspan="1" colspan="1"><?= Yii::t('app/superadmin', 'statuses.list.column_error') ?></th>
        <th class="sorting" tabindex="0" aria-controls="data-table" rowspan="1" colspan="1"><?= Yii::t('app/superadmin', 'statuses.list.column_curl_error') ?></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach (SpecialCharsHelper::multiPurifier($senders) as $id => $sender) : ?>
        <tr role="row">
            <td><?= $sender['provider'] ?></td>
            <td><?= $sender['send_method'] ?></td>
            <td><?= $sender['all_status'] ?></td>
            <td><?= $sender['good'] ?></td>
            <td><?= $sender['error'] ?></td>
            <td><?= $sender['curl_error'] ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
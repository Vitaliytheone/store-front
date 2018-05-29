<?php
use yii\widgets\LinkPager;

/** @var \yii\data\Pagination $pagination */
/** @var array $logs */
?>

<div class="container-fluid mt-3">

    <table class="table table-border">
        <thead>
        <tr>
            <th><?= Yii::t('app/superadmin', 'logs.status.list.column_id') ?></th>
            <th><?= Yii::t('app/superadmin', 'logs.status.list.column_project_type') ?></th>
            <th><?= Yii::t('app/superadmin', 'logs.status.list.column_panel') ?></th>
            <th><?= Yii::t('app/superadmin', 'logs.status.list.column_status') ?></th>
            <th><?= Yii::t('app/superadmin', 'logs.status.list.column_date') ?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($logs as $id => $log): ?>
            <tr>
                <td><?= $id ?></td>
                <td><?= $log['project_type'] ?></td>
                <td><?= $log['domain'] ?></td>
                <td><?= $log['status'] ?></td>
                <td><?= $log['date'] ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <div class="text-align-center pager">
        <?= LinkPager::widget([
            'pagination' => $pagination,
        ]); ?>
    </div>

</div>
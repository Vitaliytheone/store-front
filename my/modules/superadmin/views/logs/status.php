<?php
use yii\widgets\LinkPager;

/** @var \yii\data\Pagination $pagination */
/** @var array $logs */
?>


    <table class="table table-sm table-custom">
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

<div class="row">
    <div class="col-md-6">
        <nav>
            <ul class="pagination">
                <?= LinkPager::widget(['pagination' => $pagination,]); ?>
            </ul>
        </nav>
    </div>
</div>

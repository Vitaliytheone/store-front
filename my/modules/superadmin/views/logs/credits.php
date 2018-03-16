<?php
use yii\widgets\LinkPager;
use \my\helpers\Url;

/** @var \yii\data\Pagination $pagination */
/** @var array $logs */
?>

<div class="container-fluid mt-3">

    <table class="table table-border">
        <thead>
        <tr>
            <th><?= Yii::t('app/superadmin', 'logs.credits.list.column_id') ?></th>
            <th><?= Yii::t('app/superadmin', 'logs.credits.list.column_admin') ?></th>
            <th><?= Yii::t('app/superadmin', 'logs.credits.list.column_invoice') ?></th>
            <th><?= Yii::t('app/superadmin', 'logs.credits.list.column_credit') ?></th>
            <th><?= Yii::t('app/superadmin', 'logs.credits.list.column_memo') ?></th>
            <th><?= Yii::t('app/superadmin', 'logs.credits.list.column_created') ?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($logs as $id => $log): ?>
            <tr>
                <td><?= $id ?></td>
                <td><?= $log['admin_name'] ?></td>
                <td><a href="<?= Url::toRoute(['/invoices', 'id' => $log['invoice_id']]) ?>"> <?= $log['invoice_id'] ?></a></td>
                <td><?= $log['credit'] ?></td>
                <td class="break-all"><?= $log['memo'] ?></td>
                <td><?= $log['created_at'] ?></td>
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
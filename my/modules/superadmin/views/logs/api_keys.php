<?php
use yii\widgets\LinkPager;

/** @var \yii\data\Pagination $pagination */
/** @var array $logs */

?>

<div class="container-fluid mt-3">

    <table class="table table-border">
        <thead>
        <tr>
            <th><?= Yii::t('app/superadmin', 'logs.api_keys.list.column_id') ?></th>
            <th><?= Yii::t('app/superadmin', 'logs.api_keys.list.column_panel') ?></th>
            <th><?= Yii::t('app/superadmin', 'logs.api_keys.list.column_account') ?></th>
            <th><?= Yii::t('app/superadmin', 'logs.api_keys.list.column_provider') ?></th>
            <th><?= Yii::t('app/superadmin', 'logs.api_keys.list.column_key') ?></th>
            <th><?= Yii::t('app/superadmin', 'logs.api_keys.list.column_in_use') ?></th>
            <th><?= Yii::t('app/superadmin', 'logs.api_keys.list.column_date') ?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($logs as $id => $log): ?>
            <tr>
                <td><?= $id ?></td>
                <td><?= $log['site'] ?></td>
                <td>
                    <?php if($log['admin_login'] === 0): ?>
                    <?php elseif($log['admin_id'] > 99999990): ?>superadmin-id <?= $log['admin_id'] ?>
                    <?php else: ?><?= $log['admin_login'] ?>
                    <?php endif; ?>
                </td>
                <td><?= $log['provider'] ?></td>
                <td class="break-all">
                    <?php if(!empty($log['login'])): ?> <?= $log['login'] ?> <br> <?php endif; ?>
                    <?php if(!empty($log['passwd'])): ?> <?= $log['passwd'] ?> <br> <?php endif; ?>
                    <?php if(!empty($log['apiKey'])): ?> <?= $log['apiKey'] ?> <br> <?php endif; ?>
                </td>
                <td>
                    <?php foreach ($log['matched_projects'] as $project): ?>
                        <?= $project['site'] ?> <?php if ($project['common_customer']): ?>&copy;<?php endif; ?> <br>
                    <?php endforeach; ?>
                </td>
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
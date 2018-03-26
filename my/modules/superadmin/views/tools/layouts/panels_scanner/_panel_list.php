<?php

/* @var $this yii\web\View */
/* @var $panels array */

?>

<table class="table table-border">
    <thead>
    <tr>
        <th><?= Yii::t('app/superadmin', 'tools.levopanel.list.column_id')?></th>
        <th><?= Yii::t('app/superadmin', 'tools.levopanel.list.column_domain')?></th>
        <th><?= Yii::t('app/superadmin', 'tools.levopanel.list.column_status')?></th>
        <th><?= Yii::t('app/superadmin', 'tools.levopanel.list.column_created')?></th>
        <th><?= Yii::t('app/superadmin', 'tools.levopanel.list.column_updated')?></th>
    </tr>
    </thead>
    <tbody>
        <?php foreach ($panels as $panel) : ?>
            <tr>
                <td>
                    <?= $panel['panel_id'] ?>
                </td>
                <td>
                    <?= $panel['domain'] ?>
                </td>
                <td>
                    <?= $panel['status_name'] ?>
                </td>
                <td>
                    <?= $panel['created_at_f'] ?>
                </td>
                <td>
                    <?= $panel['updated_at_f'] ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>




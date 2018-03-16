<?php

/* @var $this \yii\web\View */
/* @var $logItems \my\models\search\ActivitySearch */

?>
<?php foreach ($logItems['models'] as $logItem) : ?>
    <tr>
        <td>
            <?= $logItem['id'] ?>
        </td>
        <td nowrap="">
            <?= $logItem['date'] ?>
        </td>
        <td>
            <?= $logItem['account'] ?>
        </td>
        <td>
            <?= $logItem['event'] ?>
        </td>
        <td>
            <?= $logItem['details'] ?>
        </td>
        <td>
            <?= $logItem['ip'] ?>
        </td>
    </tr>
<?php endforeach; ?>
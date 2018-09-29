<?php

/* @var $this yii\web\View */
/* @var $reports array */

use my\helpers\SpecialCharsHelper;

?>

<table class="table table-border">
    <thead>
    <tr>
        <!-- Добавить переводы! -->
        <th>ID</th>
        <th>Customer</th>
        <th>Subject</th>
        <th>Status</th>
        <th>Created</th>
        <th>Updated</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach (SpecialCharsHelper::multiPurifier($reports) as $report) : ?>
        <tr>
            <td>
                <?= $report['id'] ?>
            </td>
            <td>
                <?= $report['customer'] ?>
            </td>
            <td>
                <?= $report['subject'] ?>
            </td>
            <td>
                <?= $report['status'] ?>
            </td>
            <td>
                <?= $report['created_at'] ?>
            </td>
            <td>
                <?= $report['updated_at'] ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>




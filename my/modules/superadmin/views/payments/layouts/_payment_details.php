<?php
/* @var $this yii\web\View */
/* @var $logs \common\models\panels\PaymentsLog */
/* @var $log \common\models\panels\PaymentsLog */
?>
<?php if (!empty($logs)) : ?>
<strong><?= Yii::t('app/superadmin', 'payments.list.details_modal_logs_header')?></strong><br />
<?php foreach ($logs as $log) : ?>
<pre>
<?= $log->getFormattedDate('date', 'php:Y-m-d H:i:s');?><br /><?php print_r($log->getResponse()); ?>
</pre>
<?php endforeach; ?>
<?php endif; ?>
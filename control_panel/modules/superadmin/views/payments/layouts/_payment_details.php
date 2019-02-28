<?php
/* @var $this yii\web\View */
/* @var $logs \common\models\sommerces\PaymentsLog */
/* @var $log \common\models\sommerces\PaymentsLog */
?>
<?php if (!empty($logs)) : ?>
<Strong>IP: </Strong><?= $logs[0]->getIp() ?><br />
<strong><?= Yii::t('app/superadmin', 'payments.list.details_modal_logs_header')?></strong><br />
<?php foreach ($logs as $log) : ?>
<pre>
<?= $log->getFormattedDate('date', 'php:Y-m-d H:i:s');?><br /><?php print_r($log->getResponse()); ?>
</pre>
<?php endforeach; ?>
<?php endif; ?>
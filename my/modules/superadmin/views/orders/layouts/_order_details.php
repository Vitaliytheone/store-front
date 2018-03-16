<?php
    /* @var $this yii\web\View */
    /* @var $order \common\models\panels\Orders */
    /* @var $logs \common\models\panels\ThirdPartyLog */
    /* @var $log \common\models\panels\ThirdPartyLog */
?>
<strong>Details</strong><br />
<pre>
<?php print_r($order->getDetails()); ?>
</pre>

<?php if (!empty($logs)) : ?>
<strong>Logs</strong><br />

<?php foreach ($logs as $log) : ?>
<pre>
<?= $log->getFormattedDate('created_at', 'php:Y-m-d H:i:s');?> <br />
<?= $log->code; ?>


<?php print_r($log->getDetails()); ?>
</pre>
<?php endforeach; ?>
<?php endif; ?>
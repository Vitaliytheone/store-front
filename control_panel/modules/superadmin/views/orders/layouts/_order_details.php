<?php
    /* @var $this yii\web\View */
    /* @var $order \common\models\sommerces\Orders */
    /* @var $logs \common\models\sommerces\ThirdPartyLog */
    /* @var $log \common\models\sommerces\ThirdPartyLog */
?>
<strong><?= Yii::t('app/superadmin', 'orders.modal.header_details') ?></strong><br />
<pre>
<?php print_r($order->getDetails()); ?>
</pre>

<?php if (!empty($logs)) : ?>
<strong><?= Yii::t('app/superadmin', 'orders.modal.header_logs') ?></strong><br />

<?php foreach ($logs as $log) : ?>
<pre>
<?= $log->getFormattedDate('created_at', 'php:Y-m-d H:i:s');?> <br />
<?= $log->code; ?>


<?php print_r($log->getDetails()); ?>
</pre>
<?php endforeach; ?>
<?php endif; ?>
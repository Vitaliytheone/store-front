<?php
    /* @var $this yii\web\View */
    /* @var $domain \common\models\sommerces\Domains */
    /* @var $logs \common\models\sommerces\ThirdPartyLog */
    /* @var $log \common\models\sommerces\ThirdPartyLog */
?>
<strong><?= Yii::t('app/superadmin', 'domains.list.details_modal_details_header')?></strong><br />
<pre>
<?php print_r($domain->getDetails()); ?>
</pre>

<?php if (!empty($logs)) : ?>
<strong><?= Yii::t('app/superadmin', 'domains.list.details_modal_logs_header')?></strong><br />

<?php foreach ($logs as $log) : ?>
<pre>
<?= $log->getFormattedDate('created_at', 'php:Y-m-d H:i:s');?> <br />
<?= $log->code; ?>


<?php print_r($log->getDetails()); ?>
</pre>
<?php endforeach; ?>
<?php endif; ?>
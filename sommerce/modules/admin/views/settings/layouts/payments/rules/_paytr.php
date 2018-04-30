<?php
/** @var $store \common\models\stores\Stores */
$siteUrl = $store->getBaseSite();
?>

<ul>
    <li><?= Yii::t('admin', 'settings.payments_paytr_guide_1') ?></li>
    <li><?= Yii::t('admin', 'settings.payments_paytr_guide_2') ?><code><?= $siteUrl . '/paytr' ?></code></li>
</ul>



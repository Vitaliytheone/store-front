<?php
/** @var $store \common\models\stores\Stores */
$siteUrl = $store->getBaseSite();
?>

<ol>
    <li><?= Yii::t('admin', 'settings.payments_paywant_guide_1') ?><code><?= $siteUrl ?></code></li>
    <li><?= Yii::t('admin', 'settings.payments_paywant_guide_2') ?><code><?= Yii::t('admin', 'settings.payments_paywant_guide_2-1') ?></code></li>
    <li><?= Yii::t('admin', 'settings.payments_paywant_guide_3') ?><code><?= $siteUrl . '/paywant' ?></code>
</ol>
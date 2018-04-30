<?php
/** @var $store \common\models\stores\Stores */
$siteUrl = $store->getBaseSite();
?>

<ol>
    <li><?= Yii::t('admin', 'settings.payments_free_kassa_guide_1') ?></li>
    <li><?= Yii::t('admin', 'settings.payments_free_kassa_guide_2') ?><code><?= Yii::t('admin', 'settings.payments_free_kassa_guide_2-1') ?></code></li>
    <li><?= Yii::t('admin', 'settings.payments_free_kassa_guide_3') ?><code><?= Yii::t('admin', 'settings.payments_free_kassa_guide_3-1') ?></code></li>
    <li><?= Yii::t('admin', 'settings.payments_free_kassa_guide_4') ?><code><?= $siteUrl ?></code></li>
    <li><?= Yii::t('admin', 'settings.payments_free_kassa_guide_5') ?><code> <?= $siteUrl . '/freekassa' ?></code></li>
    <li><?= Yii::t('admin', 'settings.payments_free_kassa_guide_6') ?><code> <?= $siteUrl . '/cart' ?></code></li>
    <li><?= Yii::t('admin', 'settings.payments_free_kassa_guide_7') ?><code> <?= $siteUrl . '/cart' ?></code></li>
</ol>
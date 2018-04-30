<?php
/** @var $store \common\models\stores\Stores */
$siteUrl = $store->getBaseSite();
?>

<ol>
    <li><?= Yii::t('admin', 'settings.payments_webmoney_guide_1') ?></li>
    <li><?= Yii::t('admin', 'settings.payments_webmoney_guide_2') ?><a href="https://merchant.wmtransfer.com/conf/purses.asp" target="_blank">https://merchant.wmtransfer.com/conf/purses.asp</a>
    <li><?= Yii::t('admin', 'settings.payments_webmoney_guide_3') ?>
    <li><?= Yii::t('admin', 'settings.payments_webmoney_guide_4-1') ?> <i><?= Yii::t('admin', 'settings.payments_webmoney_guide_4-2') ?></i> <?= Yii::t('admin', 'settings.payments_webmoney_guide_4-3') ?> <?php switch ($store->currency) {
            case "RUB":
                echo 'WMR';
                break;
            case "USD":
                echo 'WMZ';
                break;
            case "EUR":
                echo 'WME';
                break;
        } ?>  <?= Yii::t('admin', 'settings.payments_webmoney_guide_4-4') ?>
        <ul>
            <li><?= Yii::t('admin', 'settings.payments_webmoney_guide_4-4-1-1') ?><code><?= Yii::t('admin', 'settings.payments_webmoney_guide_4-4-1-2') ?></code></li>
            <li><?= Yii::t('admin', 'settings.payments_webmoney_guide_4-4-2') ?></li>
            <li><?= Yii::t('admin', 'settings.payments_webmoney_guide_4-4-3') ?></li>
            <li><?= Yii::t('admin', 'settings.payments_webmoney_guide_4-4-4') ?><code><?= $siteUrl . '/webmoney' ?></code></li>
            <li><?= Yii::t('admin', 'settings.payments_webmoney_guide_4-4-5') ?><code><?= $siteUrl . '/cart' ?></code></li>
            <li><?= Yii::t('admin', 'settings.payments_webmoney_guide_4-4-6') ?><code><?= $siteUrl . '/cart' ?></code></li>
            <li><?= Yii::t('admin', 'settings.payments_webmoney_guide_4-4-7') ?><code><?= Yii::t('admin', 'settings.payments_webmoney_guide_4-4-7-1') ?></code></li>
        </ul>
    <li><?= Yii::t('admin', 'settings.payments_webmoney_guide_5') ?>
</ol>
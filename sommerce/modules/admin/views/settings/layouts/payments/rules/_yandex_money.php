<?php
    /** @var $store \common\models\stores\Stores */
?>

<ol>
    <li><?= Yii::t('admin', 'settings.payments_yandex_money_guide_1') ?> <a href="https://money.yandex.ru/myservices/online.xml" target="_blank">https://money.yandex.ru/myservices/online.xml</a>
    <li><?= Yii::t('admin', 'settings.payments_yandex_money_guide_2') ?>
        <ul>
            <li><?= Yii::t('admin', 'settings.payments_yandex_money_guide_2-1') ?>
            <li><?= Yii::t('admin', 'settings.payments_yandex_money_guide_2-2') ?> <code><?= $store->getBaseSite() . '/yandex' ?></code>
        </ul>
    <li><?= Yii::t('admin', 'settings.payments_yandex_money_guide_3') ?>
</ol>
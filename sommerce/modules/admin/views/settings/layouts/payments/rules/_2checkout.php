<?php
/** @var $store \common\models\stores\Stores */
?>

<ol>
    <li><?= Yii::t('admin', 'settings.payments_2checkout_guide_1') ?></li>
    <li>
        <?= Yii::t('admin', 'settings.payments_2checkout_guide_2') ?> <a href="https://www.2checkout.com/va/notifications/" target="_blank">https://www.2checkout.com/va/notifications/</a>
        <ul>
            <li><?= Yii::t('admin', 'settings.payments_2checkout_guide_2-1-1') ?>  <code><?= Yii::t('admin', 'settings.payments_2checkout_guide_2-1-2', ['store_site' => $store->getBaseSite()]) ?></code></li>
            <li><i><?= Yii::t('admin', 'settings.payments_2checkout_guide_2-2-1') ?></i> <?= Yii::t('admin', 'settings.payments_2checkout_guide_2-2-2') ?> <code><?= Yii::t('admin', 'settings.payments_2checkout_guide_2-2-3', ['store_site' => $store->getBaseSite()]) ?></code></li>
            <li><i><?= Yii::t('admin', 'settings.payments_2checkout_guide_2-3-1') ?></i> <?= Yii::t('admin', 'settings.payments_2checkout_guide_2-3-2') ?> <code><?= Yii::t('admin', 'settings.payments_2checkout_guide_2-3-3', ['store_site' => $store->getBaseSite()]) ?></code></li>
            <li><?= Yii::t('admin', 'settings.payments_2checkout_guide_2-4') ?></li>
        </ul>
    </li>
    <li>
        <?= Yii::t('admin', 'settings.payments_2checkout_guide_3') ?> <a href="https://www.2checkout.com/va/acct/detail_company_info" target="_blank">https://www.2checkout.com/va/acct/detail_company_info</a>
        <ul>
            <li><?= Yii::t('admin', 'settings.payments_2checkout_guide_3-1-1') ?> <code><?= Yii::t('admin', 'settings.payments_2checkout_guide_3-1-2') ?></code></li>
            <li><?= Yii::t('admin', 'settings.payments_2checkout_guide_3-2-1') ?> <code><?= Yii::t('admin', 'settings.payments_2checkout_guide_3-2-2') ?></code></li>
            <li><?= Yii::t('admin', 'settings.payments_2checkout_guide_3-3-1') ?> <code><?= Yii::t('admin', 'settings.payments_2checkout_guide_3-3-2') ?></code></li>
            <li><?= Yii::t('admin', 'settings.payments_2checkout_guide_3-4-1') ?> <code><?= Yii::t('admin', 'settings.payments_2checkout_guide_3-4-2', ['store_site' => $store->getBaseSite()]) ?></code></li>
            <li><?= Yii::t('admin', 'settings.payments_2checkout_guide_3-5-1') ?></li>
            <li><?= Yii::t('admin', 'settings.payments_2checkout_guide_3-6-1') ?></li>
        </ul>
    </li>
    <li><?= Yii::t('admin', 'settings.payments_2checkout_guide_4') ?></li>
</ol>

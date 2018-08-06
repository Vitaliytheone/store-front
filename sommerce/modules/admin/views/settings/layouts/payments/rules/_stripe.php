<?php
    /** @var $store \common\models\stores\Stores */
?>
<ol>
    <li><?= Yii::t('admin', 'settings.payments_stripe_guide_1', [
            'signup_url' => '<a href="https://dashboard.stripe.com/account/apikeys" target="_blank">https://dashboard.stripe.com/account/apikeys</a>'
        ]) ?>
    </li>
    <li>
        <?= Yii::t('admin', 'settings.payments_stripe_guide_2', [
            'url' => '<a href="https://dashboard.stripe.com/account/webhooks" target="_blank">https://dashboard.stripe.com/account/webhooks</a>'
        ]) ?>
        <?= Yii::t('admin', 'settings.payments_stripe_guide_3') ?> <code><?= $store->getBaseSite() . '/stripe' ?></code>
    </li>
    <li>
        <?= Yii::t('admin', 'settings.payments_stripe_guide_4') ?>
    </li>
</ol>
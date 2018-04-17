<?php
    use sommerce\modules\admin\models\forms\EditPaymentMethodForm;
    /* @var $method string */
   /** @var \common\models\stores\Stores $store */
   $store = Yii::$app->store->getInstance();
?>

<?php if(EditPaymentMethodForm::METHOD_PAYPAL === $method): ?>
<ol>
    <li>
        <?= Yii::t('admin', 'settings.payments_paypal_guide_1') ?>
    </li>
    <li>
        <?= Yii::t('admin', 'settings.payments_paypal_guide_2',[
            'api_credentials_url' => '<a href="https://www.paypal.com/us/cgi-bin/webscr?cmd=_get-api-signature&generic-flow=true" target="_blank">API Credentials</a>'
        ]) ?>
    </li>
    <li>
        <?= Yii::t('admin', 'settings.payments_paypal_guide_3') ?>
    </li>
</ol>
<?php endif; ?>

<?php if (EditPaymentMethodForm::METHOD_2CHECKOUT === $method): ?>
    <ol>
        <li><?= Yii::t('admin', 'settings.payments_2checkout_guide_1') ?></li>
        <li>
            <?= Yii::t('admin', 'settings.payments_2checkout_guide_2') ?> <a href="https://www.2checkout.com/va/notifications/" target="_blank">https://www.2checkout.com/va/notifications/</a>
            <ul>
                <li><?= Yii::t('admin', 'settings.payments_2checkout_guide_2-1-1') ?>  <code><?= Yii::t('admin', 'settings.payments_2checkout_guide_2-1-2', ['store_domain' => $store->domain]) ?></code></li>
                <li><i><?= Yii::t('admin', 'settings.payments_2checkout_guide_2-2-1') ?></i> <?= Yii::t('admin', 'settings.payments_2checkout_guide_2-2-2') ?> <code><?= Yii::t('admin', 'settings.payments_2checkout_guide_2-2-3', ['store_domain' => $store->domain]) ?></code>
                <li><i><?= Yii::t('admin', 'settings.payments_2checkout_guide_2-3-1') ?></i> <?= Yii::t('admin', 'settings.payments_2checkout_guide_2-3-2') ?> <code><?= Yii::t('admin', 'settings.payments_2checkout_guide_2-3-3', ['store_domain' => $store->domain]) ?></code>
                <li><?= Yii::t('admin', 'settings.payments_2checkout_guide_2-4') ?></li>
            </ul>
        </li>
        <li>
            <?= Yii::t('admin', 'settings.payments_2checkout_guide_3') ?> <a href="https://www.2checkout.com/va/acct/detail_company_info" target="_blank">https://www.2checkout.com/va/acct/detail_company_info</a>
            <ul>
                <li><?= Yii::t('admin', 'settings.payments_2checkout_guide_3-1-1') ?> <code><?= Yii::t('admin', 'settings.payments_2checkout_guide_3-1-2') ?></code>
                <li><?= Yii::t('admin', 'settings.payments_2checkout_guide_3-2-1') ?> <code><?= Yii::t('admin', 'settings.payments_2checkout_guide_3-2-2') ?></code>
                <li><?= Yii::t('admin', 'settings.payments_2checkout_guide_3-3-1') ?> <code><?= Yii::t('admin', 'settings.payments_2checkout_guide_3-3-2') ?></code>
                <li><?= Yii::t('admin', 'settings.payments_2checkout_guide_3-4-1') ?> <code><?= Yii::t('admin', 'settings.payments_2checkout_guide_3-4-2', ['store_domain' => $store->domain]) ?></code>
                <li><?= Yii::t('admin', 'settings.payments_2checkout_guide_3-5-1') ?></li>
                <li><?= Yii::t('admin', 'settings.payments_2checkout_guide_3-6-1') ?></li>
            </ul>
        </li>
        <li><?= Yii::t('admin', 'settings.payments_2checkout_guide_4') ?></li>
    </ol>
<?php endif; ?>

<?php if (EditPaymentMethodForm::METHOD_COINPAYMENTS === $method): ?>
<ol>
    <li>
        <?= Yii::t('admin', 'settings.payments_coinpayments_guide_1', [
            'signup_url' => '<a href="https://www.coinpayments.net/" target="_blank">CoinPayments</a>'
        ]) ?>

    </li>
    <li>
        <?= Yii::t('admin', 'settings.payments_coinpayments_guide_2') ?>
        <ul>
            <li><?= Yii::t('admin', 'settings.payments_coinpayments_guide_2_1') ?></li>
            <li><?= Yii::t('admin', 'settings.payments_coinpayments_guide_2_2') ?></li>
        </ul>
    </li>
    <li>
        <?= Yii::t('admin', 'settings.payments_coinpayments_guide_3') ?>
    </li>
</ol>
<?php endif; ?>


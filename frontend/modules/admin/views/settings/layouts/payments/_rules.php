<?php
    use frontend\modules\admin\models\forms\EditPaymentMethodForm;
    /* @var $method string */
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
    <li>
        <?= Yii::t('admin', 'settings.payments_2checkout_guide_1') ?>
    </li>
    <li>
        <?= Yii::t('admin', 'settings.payments_2checkout_guide_2') ?>
    </li>
    <li>
        <?= Yii::t('admin', 'settings.payments_2checkout_guide_3') ?>
    </li>
</ol>
<?php endif; ?>

<?php if (EditPaymentMethodForm::METHOD_BITCOIN === $method): ?>
<ol>
    <li>
        <?= Yii::t('admin', 'settings.payments_bitcoin_guide_1', [
            'myselium_url' => '<a href="https://gear.mycelium.com/" target="_blank">Mycelium Gear</a>'
        ]) ?>

    </li>
    <li>
        <?= Yii::t('admin', 'settings.payments_bitcoin_guide_2', [
            'mycelium_gateway_url' => '<a href="https://admin.gear.mycelium.com/gateways/new" target="_blank">https://admin.gear.mycelium.com/gateways/new</a>'
        ]) ?>
        <ul>
            <li>
                <?= Yii::t('admin', 'settings.payments_bitcoin_guide_2_1', [
                    'callback_url' => '<code>http://twig.perfectpanel.net/bitcoin</code>',
                ]) ?>
            </li>
            <li>
                <?= Yii::t('admin', 'settings.payments_bitcoin_guide_2_2', [
                    'redirect_url' => '<code>http://twig.perfectpanel.net/addfunds</code>',
                ]) ?>
            </li>
            <li>
                <?= Yii::t('admin', 'settings.payments_bitcoin_guide_2_3', [
                    'back_url' => '<code>http://twig.perfectpanel.net/addfunds</code>',
                ]) ?>
            </li>
            <li>
                <i><?= Yii::t('admin', 'settings.payments_bitcoin_guide_2_4') ?></i>
            </li>
        </ul>
    </li>
    <li>
        <?= Yii::t('admin', 'settings.payments_bitcoin_guide_3') ?>
    </li>
</ol>
<?php endif; ?>

<?php if (EditPaymentMethodForm::METHOD_COINPAYMENTS === $method): ?>
<ol>
    <li>
        <?= Yii::t('admin', 'settings.payments_coinpayments_guide_1', [
            'signup_url' => '<a href="https://www.coinpayments.net/" target="_blank">Coin Payments</a>'
        ]) ?>

    </li>
    <li>
        <?= Yii::t('admin', 'settings.payments_coinpayments_guide_2', [
            'coin_settings_url' => '<a href="https://www.coinpayments.net/acct-coins" target="_blank">Coin Acceptance Settings</a>'
        ]) ?>

    </li>
    <li>
    <?= Yii::t('admin', 'settings.payments_coinpayments_guide_3', [
            'account_settings_url' => '<a href="https://www.coinpayments.net/acct-settings">Account Setting</a>'
    ]) ?>
        <ul>
            <li><?= Yii::t('admin', 'settings.payments_coinpayments_guide_3_1') ?></li>
            <li><?= Yii::t('admin', 'settings.payments_coinpayments_guide_3_2') ?></li>
        </ul>
    </li>
    <li>
        <?= Yii::t('admin', 'settings.payments_coinpayments_guide_4') ?>
    </li>
</ol>
<?php endif; ?>


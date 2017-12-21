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


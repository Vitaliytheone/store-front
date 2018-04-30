<ol>
    <li><?= Yii::t('admin', 'settings.payments_paypal_guide_1') ?></li>
    <li>
        <?= Yii::t('admin', 'settings.payments_paypal_guide_2',[
            'api_credentials_url' => '<a href="https://www.paypal.com/us/cgi-bin/webscr?cmd=_get-api-signature&generic-flow=true" target="_blank">API Credentials</a>'
        ]) ?>
    </li>
    <li><?= Yii::t('admin', 'settings.payments_paypal_guide_3') ?></li>
</ol>
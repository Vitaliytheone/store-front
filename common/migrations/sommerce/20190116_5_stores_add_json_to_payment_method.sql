USE `stores`;

UPDATE `payment_methods`
SET `settings_form` = '{\"username\":{\"code\":\"username\",\"name\":\"username\",\"type\":\"input\",\"label\":\"settings.payments_paypal_username\"}, \"password\":{\"code\":\"password\",\"name\":\"password\",\"type\":\"input\",\"label\":\"settings.payments_paypal_password\"}, \"signature\":{\"code\":\"signature\",\"name\":\"signature\",\"type\":\"input\",\"label\":\"settings.payments_paypal_signature\"}, \"test_mode\":{\"code\":\"test_mode\",\"name\":\"test_mode\",\"type\":\"checkbox\",\"label\":\"settings.payments_test_mode\"}}',
    `settings_form_description` = '<ol>\r\n<li>Login to your PayPal account.</li>\r\n<li>Get your <a href=\"https://www.paypal.com/us/cgi-bin/webscr?cmd=_get-api-signature&generic-flow=true\" target=\"_blank\">API Credentials</a>.</li>\r\n<li>Enter your PayPal API details below.</li>\r\n</ol>',
    `icon` = '/img/pg/paypal.png'
WHERE `payment_methods`.`method_name` = 'paypal';

UPDATE `payment_methods`
SET `settings_form` = '{\"account_number\":{\"code\":\"account_number\",\"name\":\"account_number\",\"type\":\"input\",\"label\":\"settings.payments_2checkout_account_number\"}, \"secret_word\":{\"code\":\"secret_word\",\"name\":\"secret_word\",\"type\":\"input\",\"label\":\"settings.payments_2checkout_secret_word\"}, \"test_mode\":{\"code\":\"test_mode\",\"name\":\"test_mode\",\"type\":\"checkbox\",\"label\":\"settings.payments_test_mode\"}}',
    `settings_form_description` = '<ol>\r\n<li>Login to your 2Checkout account</li>\r\n<li>Go to <a href=\"https://www.2checkout.com/va/notifications/\" target=\"_blank\">https://www.2checkout.com/va/notifications/</a>\r\n<ul>\r\n<li>Global URL:  <code>{store_site}/2checkout</code></li>\r\n<li><i>Enable</i> Order Created: <code>{store_site}/2checkout</code></li>\r\n<li><i>Enable</i> Fraud Status Changed: <code>{store_site}/2checkout</code></li>\r\n<li>Click Save Settings</li>\r\n</ul></li>\r\n<li>Go to <a href=\"https://www.2checkout.com/va/acct/detail_company_info\" target=\"_blank\">https://www.2checkout.com/va/acct/detail_company_info</a>\r\n<ul>\r\n<li>Demo Setting: <code>Off</code></li>\r\n<li>Pricing Currency: <code>US Dollars</code></li>\r\n<li>Direct Return: <code>Given links back to my website</code></li>\r\n<li>Approved URL: <code>{store_site}</code></li>\r\n<li>Secret Word: set strong password</li>\r\n<li>Click Save Settings</li>\r\n</ul></li>\r\n<li>Enter your 2Checkout details below.</li>\r\n</ol>',
    `icon` = '/img/pg/2checkout.png'
WHERE `payment_methods`.`method_name` = '2checkout';

UPDATE `payment_methods`
SET `settings_form` = '{\"merchant_id\":{\"code\":\"merchant_id\",\"name\":\"merchant_id\",\"type\":\"input\",\"label\":\"settings.payments_coinpayments_merchant_id\"},\"ipn_secret\":{\"code\":\"ipn_secret\",\"name\":\"ipn_secret\",\"type\":\"input\",\"label\":\"settings.payments_coinpayments_ipn_secret\"}}',
    `settings_form_description` = '<ol>\r\n<li>Login to CoinPayments</li>\r\n<li>Go to <b>Account → Account Settings → Merchant Settings</b></li>\r\n<ul>\r\n<li>Generate IPN Secret</li>\r\n<li>Apply Changes</li>\r\n</ul>\r\n<li>Enter your Merchant ID and IPN Secret below.</li>\r\n</ol>',
    `icon` = '/img/pg/coinpayments.png'
WHERE `payment_methods`.`method_name` = 'coinpayments';

UPDATE `payment_methods`
SET `settings_form` = '{\"email\":{\"code\":\"email\",\"name\":\"email\",\"type\":\"input\",\"label\":\"settings.payments_pagseguro_email\"},\"token\":{\"code\":\"token\",\"name\":\"token\",\"type\":\"input\",\"label\":\"settings.payments_pagseguro_token\"}}',
    `settings_form_description` = '<ol>\r\n<li>Enter your email and token below.</li>\r\n</ol>',
    `icon` = '/img/pg/pagseguro.png'
WHERE `payment_methods`.`method_name` = 'pagseguro';

UPDATE `payment_methods`
SET `settings_form` = '{\"purse\":{\"code\":\"purse\",\"name\":\"purse\",\"type\":\"input\",\"label\":\"settings.payments_webmoney_purse\"},\"secret_key\":{\"code\":\"secret_key\",\"name\":\"secret_key\",\"type\":\"input\",\"label\":\"settings.payments_webmoney_secret_key\"}}',
    `settings_form_description` = '<ol>\r\n<li>To receive payments you must have minimum formal certificate with verified documents (or certificate of higher level).</li>\r\n<li>Go to <a href=\"https://merchant.wmtransfer.com/conf/purses.asp\" target=\"_blank\">https://merchant.wmtransfer.com/conf/purses.asp</a></li>\r\n<li>Enter login details.</li>\r\n<li>Click <i>change</i> for {currency} purse</li>\r\n<ul>\r\n<li>Test/Work modes: <code>work</code></li>\r\n<li>Merchant name: set your panel title</li>\r\n<li>Secret Key: set strong password</li>\r\n<li>Result URL: <code>{site}/webmoney</code></li>\r\n<li>Success URL: <code>{site}/addfunds</code></li>\r\n<li>Fail URL: <code>{site}/addfunds</code></li>\r\n<li>Control sign forming method: <code>SHA256</code></li>\r\n</ul>\r\n<li>Enter your WebMoney details below.</li>\r\n</ol>',
    `icon` = '/img/pg/webmoney.png'
WHERE `payment_methods`.`method_name` = 'webmoney';

UPDATE `payment_methods`
SET `settings_form` = '{\"wallet_number\":{\"code\":\"wallet_number\",\"name\":\"wallet_number\",\"type\":\"input\",\"label\":\"settings.payments_yandex_money_wallet_number\"},\"secret_word\":{\"code\":\"secret_word\",\"name\":\"secret_word\",\"type\":\"input\",\"label\":\"settings.payments_yandex_money_secret_word\"}}',
    `settings_form_description` = '<ol>\r\n<li>Go to <a href=\"https://money.yandex.ru/myservices/online.xml\" target=\"_blank\">https://money.yandex.ru/myservices/online.xml</a></li>\r\n<li>Enter login details.</li>\r\n<ul>\r\n<li>Secret word: set strong password</li>\r\n<li>HTTP-notices URL: <code>{site}/yandex</code></li>\r\n</ul>\r\n<li>Enter your Yandex money details below.\r\n</ol>',
    `icon` = '/img/pg/yandex_money.png'
WHERE `payment_methods`.`method_name` = 'yandexmoney';

UPDATE `payment_methods`
SET `settings_form` = '{\"merchant_id\":{\"code\":\"merchant_id\",\"name\":\"merchant_id\",\"type\":\"input\",\"label\":\"settings.payments_free_kassa_merchant_id\"},\"secret_word\":{\"code\":\"secret_word\",\"name\":\"secret_word\",\"type\":\"input\",\"label\":\"settings.payments_free_kassa_secret_word\"},\"secret_word2\":{\"code\":\"secret_word2\",\"name\":\"secret_word2\",\"type\":\"input\",\"label\":\"settings.payments_free_kassa_secret_word2\"}}',
    `settings_form_description` = '<ul>\r\n<li> Go to Cash Desk Settings </li>\r\n<li> Select <code>POST</code> Alert Method</li>\r\n<li> Select Integration Mode <code>No</code></li>\r\n<li> Site URL: <code>{site}/</code> </li>\r\n<li> Alert IPN URL: <code>{site}/freekassa</code></li>\r\n<li> return URL for success: <code>{site}/addfunds</code></li>\r\n<li> return URL in case of failure: <code>{site}/addfunds</code></i>\r\n</ul>',
    `icon` = '/img/pg/free_kassa.png'
WHERE `payment_methods`.`method_name` = 'freekassa';

UPDATE `payment_methods`
SET `settings_form` = '{\"merchant_id\":{\"code\":\"merchant_id\",\"name\":\"merchant_id\",\"type\":\"input\",\"label\":\"settings.payments_paytr_merchant_id\"},\"merchant_key\":{\"code\":\"merchant_key\",\"name\":\"merchant_key\",\"type\":\"input\",\"label\":\"settings.payments_paytr_merchant_key\"},\"merchant_salt\":{\"code\":\"merchant_salt\",\"name\":\"merchant_salt\",\"type\":\"input\",\"label\":\"settings.payments_paytr_merchant_salt\"},\"commission\":{\"code\":\"commission\",\"name\":\"commission\",\"type\":\"input\",\"label\":\"settings.payments_paytr_merchant_comission\"}}',
    `settings_form_description` = '<ul>\r\n<li>Go to Merchant Settings</li>\r\n<li>Set callback url: <code>{site}/paytr</code></li>\r\n</ul>',
    `addfunds_form` = '{\"phone\":{\"label\":\"addfunds.phone\",\"type\":\"input\",\"rules\":[{\"0\":\"phone\",\"1\":\"required\",\"message\":\"addfunds.error.phone\"},{\"0\":\"phone\",\"1\":\"string\",\"message\":\"addfunds.error.phone\"}]}}',
    `icon` = '/img/pg/paytr.png'
WHERE `payment_methods`.`method_name` = 'paytr';

UPDATE `payment_methods`
SET `settings_form` = '{\"apiKey\":{\"code\":\"apiKey\",\"name\":\"apiKey\",\"type\":\"input\",\"label\":\"settings.payments_paywant_apiKey\"},\"apiSecret\":{\"code\":\"apiSecret\",\"name\":\"apiSecret\",\"type\":\"input\",\"label\":\"settings.payments_paywant_apiSecret\"},\"fee\":{\"code\":\"fee\",\"name\":\"fee\",\"type\":\"input\",\"label\":\"settings.payments_paywant_fee\"}}',
    `settings_form_description` = '<ul>\r\n<li>Store Site: <code>{site}</code></li>\r\n<li>IP address (Site): <code>THIS SERVER IP</code></li>\r\n<li>API IPN: <code>{site}/paywant</code></li>\r\n</ul>',
    `icon` = '/img/pg/paywant.png'
WHERE `payment_methods`.`method_name` = 'paywant';

UPDATE `payment_methods`
SET `settings_form` = '{\"collectionId\":{\"code\":\"collectionId\",\"name\":\"collectionId\",\"type\":\"input\",\"label\":\"settings.payments_billplz_collectionId\"},\"secret\":{\"code\":\"secret\",\"name\":\"secret\",\"type\":\"input\",\"label\":\"settings.payments_billplz_secret\"}}',
    `settings_form_description` = '<ul>\r\n<li>Go to Merchant Settings</li>\r\n<li>Status URL: <code>{site}/billplz</code></li>\r\n<li>Success URL: <code>{site}/addfunds</code></li>\r\n<li>Fail URL: <code>{site}/addfunds</code></li>\r\n</ul>',
    `icon` = '/img/pg/billplz.png'
WHERE `payment_methods`.`method_name` = 'billplz';

UPDATE `payment_methods`
SET `settings_form` = '{\"merchant_client_key\":{\"code\":\"merchant_client_key\",\"name\":\"merchant_client_key\",\"type\":\"input\",\"label\":\"settings.payments_authorize_merchant_client_key\"},\"merchant_login_id\":{\"code\":\"merchant_login_id\",\"name\":\"merchant_login_id\",\"type\":\"input\",\"label\":\"settings.payments_authorize_merchant_login_id\"},\"merchant_transaction_id\":{\"code\":\"merchant_transaction_id\",\"name\":\"merchant_transaction_id\",\"type\":\"input\",\"label\":\"settings.payments_authorize_merchant_transaction_id\"},\"test_mode\":{\"code\":\"test_mode\",\"name\":\"test_mode\",\"type\":\"checkbox\",\"label\":\"settings.payments_test_mode\"}}',
    `settings_form_description` = '<ol>\r\n<li>Enter your merchant client key, merchant login id and merchant transaction id below.</li>\r\n</ol>',
    `addfunds_form` = '{\"data_descriptor\":{\"name\":\"data_descriptor\",\"type\":\"hidden\"},\"data_value\":{\"name\":\"data_value\",\"type\":\"hidden\"}}',
    `icon` = '/img/pg/authorize.png'
WHERE `payment_methods`.`method_name` = 'authorize';

UPDATE `payment_methods`
SET `settings_form` = '{\"wallet_number\":{\"code\":\"wallet_number\",\"name\":\"wallet_number\",\"type\":\"input\",\"label\":\"settings.payments_yandex_cards_wallet_number\"},\"secret_word\":{\"code\":\"secret_word\",\"name\":\"secret_word\",\"type\":\"input\",\"label\":\"settings.payments_yandex_cards_secret_word\"}}',
    `settings_form_description` = '<ol>\r\n<li>Go to <a href=\"https://money.yandex.ru/myservices/online.xml\" target=\"_blank\">https://money.yandex.ru/myservices/online.xml</a></li>\r\n<li>Enter login details.</li>\r\n<ul>\r\n<li>Secret word: set strong password</li>\r\n<li>HTTP-notices URL: <code>{site}/yandex</code></li>\r\n</ul>\r\n<li>Enter your Yandex money details below.</li>\r\n</ol>',
    `icon` = '/img/pg/yandex_money.png'
WHERE `payment_methods`.`method_name` = 'yandexcards';

UPDATE `payment_methods`
SET `settings_form` = '{\"public_key\":{\"code\":\"public_key\",\"name\":\"public_key\",\"type\":\"input\",\"label\":\"settings.payments_stripe_public_key\"},\"secret_key\":{\"code\":\"secret_key\",\"name\":\"secret_key\",\"type\":\"input\",\"label\":\"settings.payments_stripe_secret_key\"},\"webhook_secret\":{\"code\":\"webhook_secret\",\"name\":\"webhook_secret\",\"type\":\"input\",\"label\":\"settings.payments_stripe_webhook_secret\"}}',
    `settings_form_description` = '<ol>\r\n<li>Publishable key and Secret key you may find on <a href=\"https://dashboard.stripe.com/account/apikeys\" target=\"_blank\">https://dashboard.stripe.com/account/apikeys</a></li>\r\n<li>Go to <a href=\"https://dashboard.stripe.com/account/webhooks\" target=\"_blank\">https://dashboard.stripe.com/account/webhooks</a> and add endpoint for <code>{site}/stripe</code></li>\r\n<li>Click on created webhook to get Signing secret</li>\r\n</ol>',
    `addfunds_form` = '{\"token\":{\"name\":\"token\",\"type\":\"hidden\"},\"email\":{\"name\":\"email\",\"type\":\"hidden\"}}',
    `icon` = '/img/pg/stripe_logo.png'
WHERE `payment_methods`.`method_name` = 'stripe';

UPDATE `payment_methods`
SET `settings_form` = '{\"client_id\":{\"code\":\"client_id\",\"name\":\"client_id\",\"type\":\"input\",\"label\":\"settings.payments_mercadopago_client_id\"},\"secret\":{\"code\":\"secret\",\"name\":\"secret\",\"type\":\"input\",\"label\":\"settings.payments_mercadopago_secret\"}, \"test_mode\":{\"code\":\"test_mode\",\"name\":\"test_mode\",\"type\":\"checkbox\",\"label\":\"settings.payments_mercadopago_test_mode\"}}',
    `settings_form_description` = '<ol>\r\n<li>Enter your client_id and secret below.</li>\r\n</ol>',
    `icon` = '/img/pg/mercado_pago.png'
WHERE `payment_methods`.`method_name` = 'mercadopago';

UPDATE `payment_methods`
SET `settings_form` = '{\"email\":{\"code\":\"email\",\"name\":\"email\",\"type\":\"input\",\"label\":\"settings.payments_paypal_email\"}, \"test_mode\":{\"code\":\"test_mode\",\"name\":\"test_mode\",\"type\":\"checkbox\",\"label\":\"settings.payments_test_mode\"}}',
    `settings_form_description` = '<ol>\r\n<li>Enter your PayPal Email address below.</li>\r\n</ol>',
    `icon` = '/img/pg/paypal.png'
WHERE `payment_methods`.`method_name` = 'paypalstandard';

UPDATE `payment_methods`
SET `settings_form` = '{\"secret_key\":{\"code\":\"secret_key\",\"name\":\"secret_key\",\"type\":\"input\",\"label\":\"settings.payments_mollie_api\"}}',
    `settings_form_description` = '<ul>\r\n<li>Go to Mollie website → <a href=\"https://www.mollie.com/dashboard/developers/api-keys\" target=\"_blank\">Developer Dashboard</a> and get your <b>Live API key</b></li>\r\n<li>If you want to test payment system, use <b>Test API key</b> instead.</li>\r\n</ul>',
    `icon` = '/img/pg/mollie.png'
WHERE `payment_methods`.`method_name` = 'mollie';

UPDATE `payment_methods`
SET `settings_form` = '{\"public_key\":{\"code\":\"public_key\",\"name\":\"public_key\",\"type\":\"input\",\"label\":\"settings.payments_stripe_public_key\"},\"secret_key\":{\"code\":\"secret_key\",\"name\":\"secret_key\",\"type\":\"input\",\"label\":\"settings.payments_stripe_secret_key\"},\"webhook_secret\":{\"code\":\"webhook_secret\",\"name\":\"webhook_secret\",\"type\":\"input\",\"label\":\"settings.payments_stripe_webhook_secret\"}}',
    `settings_form_description` = '<ol>\r\n<li>Publishable key and Secret key you may find on <a href=\"https://dashboard.stripe.com/account/apikeys\" target=\"_blank\">https://dashboard.stripe.com/account/apikeys</a></li>\r\n<li>Go to <a href=\"https://dashboard.stripe.com/account/webhooks\" target=\"_blank\">https://dashboard.stripe.com/account/webhooks</a> and add endpoint for <code>{site}/stripe_3d_secure</code></li>\r\n<li>Click on created webhook to get Signing secret</li>\r\n</ol>',
    `addfunds_form` = '{\"token\":{\"name\":\"token\",\"type\":\"hidden\"},\"email\":{\"name\":\"email\",\"type\":\"hidden\"}}',
    `icon` = '/img/pg/stripe_logo.png'
WHERE `payment_methods`.`method_name` = 'stripe_3d_secure';
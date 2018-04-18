<?php

$orderLinks = require(__DIR__ . '/settings/order-link.php');
$timezone = require(__DIR__ . '/settings/timezone.php');
$cdn = require(__DIR__ . '/settings/cdn.php');
$currencies = require(__DIR__ . '/settings/currency.php');

return [
    'adminEmail' => 'admin@example.com',
    'debugIps' => ['*'],

    'auth_key' => '001100110011001100110011',

    'reCaptcha.siteKey' => '6LeAmT4UAAAAAKz1c-wjHdI2XDp_PglfA1rl8RbG',
    'reCaptcha.secret' => '6LeAmT4UAAAAAI88eDxVJkusrAurV9A8EqwphqVw',

    'getyourpanelKey' => 'j84GG5H6CfkjeHZxWzdSGqFw8TpfP2Tb',

    'mailgun.key' => 'key-cf10921abd5862ddd4b4b55692031fad',
    'mailgun.domain' => 'perfectpanel.net',

    'gearmanIp' => '127.0.0.1',
    'gearmanPort' => 4730,
    'gearmanPrefix' => 'sommerce_',
    'support_email' => 'mailgun@perfectpanel.net',

    'debugEmail' => null,
    'mailer.status' => true,

    'senderOrdersLimit' => 100,
    'getstatusOrdersLimit' => 100,

    'iconFileSizeLimit' => 0.512e6,     // Bytes
    'logoFileSizeLimit' => 2.097152e6,  // Bytes

    'orderLinks' => $orderLinks,
    'timezone' => $timezone,
    'cdn' => $cdn,
    'currencies' => $currencies,
    'devEmail' => ['myerror@13.uz'], // Адреса почты на которые шлем ошибки
];

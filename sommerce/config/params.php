<?php

$orderLinks = require(__DIR__ . '/settings/order-link.php');
$timezone = require(__DIR__ . '/settings/timezone.php');
$cdn = require(__DIR__ . '/settings/cdn.php');
$currencies = require(__DIR__ . '/settings/currency.php');
$languages = require(__DIR__ . '/settings/languages.php');

return [
    'adminEmail' => 'admin@example.com',
    'debugIps' => ['*'],

    'auth_key' => '+^e91s&qm&9*hs9_z=1e8jq8rl@njmr#=ts16!f_23wo7-@s(r', // Must be a same as my/config/params.php 'auth_key'!

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

    'senderOrdersLimit' => 100,
    'getstatusOrdersLimit' => 100,

    'iconFileSizeLimit' => 0.512e6,     // Bytes
    'logoFileSizeLimit' => 2.097152e6,  // Bytes

    'orderLinks' => $orderLinks,
    'timezone' => $timezone,
    'cdn' => $cdn,
    'currencies' => $currencies,
    'languages' => $languages,
    'devEmail' => ['myerror@13.uz'], // Адреса почты на которые шлем ошибки
];

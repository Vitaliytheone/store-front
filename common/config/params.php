<?php

$orderLinks = require(__DIR__ . '/settings/order-link.php');
$timezone = require(__DIR__ . '/settings/timezone.php');
$cdn = require(__DIR__ . '/settings/cdn.php');
$currencies = require(__DIR__ . '/settings/currency.php');

return [
    'storeId' => 1,
    'orderLinks' => $orderLinks,
    'timezone' => $timezone,
    'cdn' => $cdn,
    'currencies' => $currencies,

    'getyourpanelKey' => 'j84GG5H6CfkjeHZxWzdSGqFw8TpfP2Tb',
    'defaultTheme' => 'classic',
    'default_language' => 'en',

    'mailgun.key' => 'key-cf10921abd5862ddd4b4b55692031fad',
    'mailgun.domain' => 'perfectpanel.net',

    'gearmanIp' => '127.0.0.1',
    'gearmanPort' => 4730,
    'gearmanPrefix' => 'sommerce_',
    'support_email' => 'mailgun@perfectpanel.net',

    'debugEmail' => null,

    'senderOrdersLimit' => 100,
    'getstatusOrdersLimit' => 100,

    'mailer.status' => true,
];

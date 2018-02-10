<?php
$packageLinkTypes = require(__DIR__ . '/packageLinkTypes.php');
$timezone = require(__DIR__ . '/timezone.php');
$cdn = require(__DIR__ . '/cdn.php');

return [
    'storeId' => 1,
    'packageLinkTypes' => $packageLinkTypes,
    'timezone' => $timezone,
    'cdn' => $cdn,
    'getyourpanelKey' => 'j84GG5H6CfkjeHZxWzdSGqFw8TpfP2Tb',
    'defaultTheme' => 'classic',
    'default_language' => 'en',

    'mailgun.key' => 'key-cf10921abd5862ddd4b4b55692031fad',
    'mailgun.domain' => 'perfectpanel.net',

    'gearmanIp' => '127.0.0.1',
    'gearmanPort' => 4730,
    'gearmanPrefix' => '',
    'support_email' => 'mailgun@perfectpanel.net',

    'debugEmail' => 'shulyakwork@gmail.com',
];

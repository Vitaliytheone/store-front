<?php

$orderLinks = require(__DIR__ . '/settings/order-link.php');
$timezone = require(__DIR__ . '/settings/timezone.php');
$cdn = require(__DIR__ . '/settings/cdn.php');
$currencies = require(__DIR__ . '/settings/currency.php');

$configPath = __DIR__ . '/config.json';

$configParams = (function() use ($configPath) {
    $configParams = file_get_contents($configPath);
    return !empty($configParams) ? json_decode($configParams, true) : [];
})();

$dbParams = (function() use ($configParams) {
    if (!$configParams['db']) {
        throw new Exception('DB is not configured yet!');
    }
    return $configParams['db'];
})();

$proxyParams = (function() use ($configParams) {
    if (!$configParams['proxy']) {
        throw new Exception('Proxy is not configured yet!');
    }
    return [
        'ip' => $configParams['proxy']['ip'],
        'port' => $configParams['proxy']['port'],
    ];
})();

return [
    'orderLinks' => $orderLinks,
    'timezone' => $timezone,
    'cdn' => $cdn,
    'currencies' => $currencies,

    'config.db' => $dbParams,
    'config.proxy' => $proxyParams,
];

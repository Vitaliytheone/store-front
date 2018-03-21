<?php

$configPath = __DIR__ . '/config.json';
$configPath = file_exists($configPath) ? $configPath : '/var/www/cfg/config.json';

$configParams = (function() use ($configPath) {
    $configParams = file_exists($configPath) ? file_get_contents($configPath) : [];
    return !empty($configParams) ? json_decode($configParams, true) : [];
})();

$dbParams = (function() use ($configParams) {
    if (empty($configParams['db'])) {
        throw new Exception('DB is not configured yet!');
    }
    return $configParams['db'];
})();

$proxyParams = (function() use ($configParams) {
    if (empty($configParams['proxy'])) {
        throw new Exception('Proxy is not configured yet!');
    }
    return [
        'ip' => $configParams['proxy']['ip'],
        'port' => $configParams['proxy']['port'],
    ];
})();

return [
    'config.db' => $dbParams,
    'config.proxy' => $proxyParams,
];

<?php
Yii::setAlias('@common', dirname(__DIR__));
Yii::setAlias('@my', dirname(dirname(__DIR__)) . '/my');
Yii::setAlias('@superadmin', dirname(dirname(__DIR__)) . '/my/modules/superadmin');
Yii::setAlias('@sommerce', dirname(dirname(__DIR__)) . '/sommerce');
Yii::setAlias('@gateway', dirname(dirname(__DIR__)) . '/gateway');
Yii::setAlias('@console', dirname(dirname(__DIR__)) . '/console');
Yii::setAlias('@project_root', dirname(dirname(__DIR__)));
Yii::setAlias('@node_modules', dirname(dirname(__DIR__)) . '/node_modules');
Yii::setAlias('@libs', dirname(dirname(__DIR__)) . '/libs');
Yii::setAlias('@control_panel', dirname(dirname(__DIR__)) . '/control_panel');


$configPath = __DIR__ . '/config.json';
$configPath = file_exists($configPath) ? $configPath : '/var/www/cfg/config.json';

$configParams = (function() use ($configPath) {
    $configParams = file_exists($configPath) ? file_get_contents($configPath) : [];
    return !empty($configParams) ? json_decode($configParams, true) : [];
})();

defined('DB_CONFIG') or define('DB_CONFIG', (function() use ($configParams) {
    if (empty($configParams['db'])) {
        throw new Exception('DB is not configured yet!');
    }
    return $configParams['db'];
})());

defined('PROXY_CONFIG') or define('PROXY_CONFIG', (function() use ($configParams) {
    if (empty($configParams['proxy'])) {
        throw new Exception('Proxy is not configured yet!');
    }
    return [
        'main' => [
            'ip' => $configParams['proxy']['ip'],
            'port' => $configParams['proxy']['port'],
        ],
        'link_type' => [
            'ip' => $configParams['proxy_link_type']['ip'],
            'port' => $configParams['proxy_link_type']['port'],
        ]
    ];
})());

defined('DB_STORES') or define('DB_STORES', 'stores');
defined('DB_SOMMERCES') or define('DB_SOMMERCES', 'sommerces');
defined('DB_PANELS') or define('DB_PANELS', 'panels');
defined('DB_GATEWAYS') or define('DB_GATEWAYS', 'gateways');
defined('DB_GATEWAY') or define('DB_GATEWAY', 'gateway');

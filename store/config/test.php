<?php

$db = array_merge(
    require_once(__DIR__ . '/db.php'),
    file_exists(__DIR__ . '/db-local.php') ? require(__DIR__ . '/db-local.php') : []
);

$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    file_exists(__DIR__ . '/../../common/config/params-local.php') ? require(__DIR__ . '/../../common/config/params-local.php') : [],
    require(__DIR__ . '/params.php'),
    file_exists(__DIR__ . '/params-local.php') ? require(__DIR__ . '/params-local.php') : []
);

$configDb = DB_CONFIG;

return [
    'id' => 'app-store-tests',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'store'],
    'components' => [
        'assetManager' => [
            'basePath' => __DIR__ . '/../web/assets',
        ],
        'urlManager' => [
            'showScriptName' => true,
        ],
        'store' => [
            'class' => 'common\components\stores\StoreComponent'
        ],
        'db' => $db['db'],
        'storeDb' => $db['storeDb'],
    ],
];

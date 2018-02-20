<?php

$db = array_merge(
    require(__DIR__ . '/db.php'),
    file_exists(__DIR__ . '/db-local.php') ? require(__DIR__ . '/db-local.php') : []
);

return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=' . $db['db']['host'] . ';dbname=stores',
            'username' => $db['db']['username'],
            'password' => $db['db']['password'],
            'charset' => 'utf8',
        ],
        'storeDb' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=' . $db['store_db']['host'],
            'username' => $db['store_db']['username'],
            'password' => $db['store_db']['password'],
            'charset' => 'utf8mb4',
        ],
        'store' => [
            'class' => 'common\components\stores\StoreComponent'
        ],
    ],
];

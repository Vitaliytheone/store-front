<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=stores',
            'username' => 'sommerce',
            'password' => 'X3X8HseMomCWgHv3',
            'charset' => 'utf8',
        ],
        'storeDb' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=store',
            'username' => 'sommerce',
            'password' => 'X3X8HseMomCWgHv3',
            'charset' => 'utf8mb4',
        ],
        'store' => [
            'class' => 'common\components\stores\StoreComponent'
        ],
    ],
];

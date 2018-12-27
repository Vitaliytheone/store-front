<?php

return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=',
            'username' => '',
            'password' => '',
            'charset' => 'utf8mb4',
        ],
        'storeDb' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=',
            'username' => '',
            'password' => '',
            'charset' => 'utf8mb4',
        ],
    ],
];
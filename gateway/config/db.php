<?php

return [
    'db' => [
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host=' . DB_CONFIG[0]['host'] . ';dbname=' . DB_GATEWAYS,
        'username' => DB_CONFIG[0]['user'],
        'password' => DB_CONFIG[0]['password'],
        'charset' => 'utf8',
    ],
    'storeDb' => [
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host=' . DB_CONFIG[1]['host'],
        'username' => DB_CONFIG[1]['user'],
        'password' => DB_CONFIG[1]['password'],
        'charset' => 'utf8mb4',
    ],
];
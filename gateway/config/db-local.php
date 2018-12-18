<?php

return [
    'db' => [
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host=' . DB_CONFIG[0]['host'] . ';dbname=' . DB_GATEWAYS,
        'username' => DB_CONFIG[0]['user'],
        'password' => DB_CONFIG[0]['password'],
        'charset' => 'utf8',
    ],
    'gatewayDb' => [
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host=' . DB_CONFIG[0]['host'] . ';dbname=gateway',
        'username' => DB_CONFIG[0]['user'],
        'password' => DB_CONFIG[0]['password'],
        'charset' => 'utf8',
    ],
];
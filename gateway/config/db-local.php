<?php

return [
    'db' => [
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host=localhost;dbname=' . DB_GATEWAYS,
        'username' => 'root',
        'password' => 'root',
        'charset' => 'utf8',
    ],
    'storeDb' => [
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host=localhost;dbname=gateway_site',
        'username' => 'root',
        'password' => 'root',
        'charset' => 'utf8',
    ],
];
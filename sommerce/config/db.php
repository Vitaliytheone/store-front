<?php

$params = require(__DIR__ . '/../../common/config/params.php');

$configDb = $params['config.db'];

return [
    'db' => [
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host=' . $configDb[0]['host'] . ';dbname=stores',
        'username' => $configDb[0]['user'],
        'password' => $configDb[0]['password'],
        'charset' => 'utf8',
    ],
    'storeDb' => [
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host=' . $configDb[1]['host'],
        'username' => $configDb[1]['username'],
        'password' => $configDb[1]['password'],
        'charset' => 'utf8mb4',
    ],
];
<?php

$params = require(__DIR__ . '/../../common/config/params.php');

$configDb = $params['config.db'];

return [
    'db' => [
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host='. $configDb[0]['host'] .';dbname=panels',
        'username' => $configDb[0]['username'],
        'password' => $configDb[0]['password'],
        'charset' => 'utf8',
    ]
];
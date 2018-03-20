<?php

$params = require(__DIR__ . '/../../common/config/params.php');

$configDb = $params['config.db'];

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host='. $configDb[0]['host'] .';dbname=panels',
    'username' => $configDb[0]['user'],
    'password' => $configDb[0]['password'],
    'charset' => 'utf8',
];
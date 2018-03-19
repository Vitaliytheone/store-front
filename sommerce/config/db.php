<?php

$params = require_once(__DIR__ . '/../../common/config/params.php');

$configDb = $params['config.db'];

return [
    'db' => [
        'host' => $configDb[0]['host'],
        'username' => $configDb[0]['user'],
        'password' => $configDb[0]['password'],
    ],
    'store_db' => [
        'host' => $configDb[1]['host'],
        'username' => $configDb[1]['username'],
        'password' => $configDb[1]['password'],
    ],
];
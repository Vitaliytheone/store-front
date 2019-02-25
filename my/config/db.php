<?php

return [
    'db' => [
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host='. DB_CONFIG[0]['host'] .';dbname=' . DB_PANELS,
        'username' => DB_CONFIG[0]['user'],
        'password' => DB_CONFIG[0]['password'],
        'charset' => 'utf8',
    ],
    'panelDb' => [
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host='. DB_CONFIG[0]['host'],
        'username' => DB_CONFIG[0]['user'],
        'password' => DB_CONFIG[0]['password'],
        'charset' => 'utf8',
    ]
];
<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    file_exists(__DIR__ . '/../../common/config/params-local.php') ? require(__DIR__ . '/../../common/config/params-local.php') : [],
    require(__DIR__ . '/params.php'),
    file_exists(__DIR__ . '/params-local.php') ? require(__DIR__ . '/params-local.php') : []
);

return [
    'id' => 'app-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'console\controllers',
    'controllerMap' => [
        'fixture' => [
            'class' => 'yii\console\controllers\FixtureController',
            'namespace' => 'common\fixtures',
          ],

        'migrate-sommerce' => [
            'class' => 'yii\console\controllers\MigrateController',
            'migrationTable' => 'panels.system_migrations',
            'migrationPath' => '@common/migrations/sommerce/',
        ],

        'migrate-my' => [
            'class' => 'yii\console\controllers\MigrateController',
            'migrationTable' => 'stores.system_migrations',
            'migrationPath' => '@common/migrations/my/',
        ],
    ],
    'components' => [
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
    ],
    'params' => $params,
];

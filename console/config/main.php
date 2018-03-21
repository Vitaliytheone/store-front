<?php

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
            'class' => 'console\controllers\sommerce\CustomMigrateController',
            'migrationTable' => 'stores.system_migrations',
            'migrationPath' => '@common/migrations/sommerce/',
        ],

        'migrate-my' => [
            'class' => 'console\controllers\my\CustomMigrateController',
            'migrationTable' => 'panels.system_migrations',
            'migrationPath' => '@common/migrations/my/',
        ],

        'cron-sommerce' => [
            'class' => 'console\controllers\sommerce\CronController',
        ],

        'system-sommerce' => [
            'class' => 'console\controllers\sommerce\SystemController',
        ],

        'system-my' => [
            'class' => 'console\controllers\my\SystemController',
        ],

        'cron-my' => [
            'class' => 'console\controllers\my\CronController',
        ],

        'panel-scanner-my' => [
            'class' => 'console\controllers\my\PanelScannerController',
        ],

        'template-my' => [
            'class' => 'console\controllers\my\TemplateController',
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
    'params' => [],
];

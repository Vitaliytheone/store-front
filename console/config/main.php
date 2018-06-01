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
            'migrationTable' => DB_STORES . '.system_migrations',
            'migrationPath' => '@common/migrations/sommerce/',
        ],

        'migrate-my' => [
            'class' => 'console\controllers\my\CustomMigrateController',
            'migrationTable' => DB_PANELS . '.system_migrations',
            'migrationPath' => '@common/migrations/my/',
        ],

        'cron-sommerce' => [
            'class' => 'console\controllers\sommerce\CronController',
        ],

        'system-sommerce' => [
            'class' => 'console\controllers\sommerce\SystemController',
        ],

        'blocks-sommerce' => [
            'class' => 'console\controllers\sommerce\BlocksController',
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

        'store' => [
            'class' => 'common\components\stores\StoreComponent'
        ],

        'view' => [
            'class' => 'common\components\View',
            'renderers' => [
                'twig' => [
                    'class' => 'common\components\twig\ViewRenderer',
                    'cachePath' => null,
                    'options' => [
                        'autoescape' => false
                    ],
                ],
            ],
        ],
    ],
    'params' => array_merge(
        require(__DIR__ . '/../../common/config/params.php'),
        file_exists(__DIR__ . '/../../common/config/params-local.php') ? require(__DIR__ . '/../../common/config/params-local.php') : [],
        require(__DIR__ . '/params.php'),
        file_exists(__DIR__ . '/params-local.php') ? require(__DIR__ . '/params-local.php') : []
    ),
];

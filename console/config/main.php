<?php

$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    file_exists(__DIR__ . '/../../common/config/params-local.php') ? require(__DIR__ . '/../../common/config/params-local.php') : [],
    require(__DIR__ . '/params.php'),
    file_exists(__DIR__ . '/params-local.php') ? require(__DIR__ . '/params-local.php') : []
);

$config = [
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

        'migrate-gateway' => [
            'class' => 'console\controllers\gateway\CustomMigrateController',
            'migrationTable' => DB_GATEWAYS . '.system_migrations',
            'migrationPath' => '@common/migrations/gateway/',
        ],

        'migrate' => [
            'class' => 'console\controllers\my\CustomMigrateController',
            'migrationTable' => DB_PANELS . '.system_migrations',
            'migrationPath' => '@common/migrations/my/',
        ],

        'migrate-my' => [
            'class' => 'console\controllers\my\CustomMigrateController',
            'migrationTable' => DB_PANELS . '.system_migrations',
            'migrationPath' => '@common/migrations/my/',
        ],

        'migrate-control-panel' => [
            'class' => 'console\controllers\control_panel\CustomMigrateController',
            'migrationTable' => DB_PANELS . '.system_migrations',
            'migrationPath' => '@common/migrations/control_panel/',
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

        'system-gateway' => [
            'class' => 'console\controllers\gateway\SystemController',
        ],

        'system-control-panel' => [
            'class' => 'console\controllers\control_panel\SystemController',
        ],

        'cron-control-panel' => [
            'class' => 'console\controllers\control_panel\CronController',
        ],

        'template-control-panel' => [
            'class' => 'console\controllers\control_panel\TemplateController',
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
        'gateway' => [
            'class' => 'common\components\gateways\GatewayComponent'
        ],

        'view' => [
            'class' => 'sommerce\components\View',
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

        'mailerSwift' => [
            'class' => 'yii\swiftmailer\Mailer',
            
            // раскомментировать если использовать smtp отправку и наоборот
            /*'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => $params['swift.host'], //вставляем имя или адрес почтового сервера
                'username' => $params['swift.username'],
                'password' => $params['swift.password'],
                'port' => $params['swift.port'],
            ],*/
            'transport' => [
                'class' => 'Swift_MailTransport',
            ],
            'viewPath' => '@my/mail/views',
        ],

        'urlManager' => [
            'class' => 'yii\web\UrlManager',
            'enablePrettyUrl' => true,
            'showScriptName' => false,
        ],
    ],
    'params' => $params,
];

if (!empty($params['devEmail'])) {
    $config['components']['log']['targets'][] = [
        'class' => 'yii\log\EmailTarget',
        'mailer' => 'mailerSwift',
        'levels' => ['error'],
        'message' => [
            'from' => ['yii2errorlog@13.uz'],
            'to' =>  $params['devEmail'],
            'subject' => 'Error sommerce console app',
        ],
        'except' => [
            'yii\i18n\PhpMessageSource::loadMessages',
            'yii\i18n\PhpMessageSource::loadFallbackMessages'
        ],
    ];
}

return $config;
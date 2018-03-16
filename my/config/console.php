<?php

Yii::setAlias('@tests', dirname(__DIR__) . '/tests/codeception');

$params = array_merge(
    require(__DIR__ . '/params.php'),
    file_exists(__DIR__ . '/params-local.php') ? require(__DIR__ . '/params-local.php') : []
);

$db = array_merge(
    require(__DIR__ . '/db.php'),
    file_exists(__DIR__ . '/db-local.php') ? require(__DIR__ . '/db-local.php') : []
);

$config = [
    'id' => 'basic-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'app\commands',
    'language' => 'en-US',
    'components' => [
        'mailer' => [
            'class' => 'app\components\mailer\mailgun\Mailer',
            'key' => $params['mailgun.key'],
            'domain' => $params['mailgun.domain'],
            'viewPath' => '@app/mail/views',
        ],
        'mailerSwift' => [
            'class' => 'yii\swiftmailer\Mailer',
            // раскомментировать если использовать smtp отправку и наоборот
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'ssl://smtp.yandex.ru', //вставляем имя или адрес почтового сервера
                'username' => 'noreply@getyourpanel.com',
                'password' => 'T8XXFqT4IS',
                'port' => '465',
            ],
            'viewPath' => '@app/mail/views',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error'],
                    'except' => [
                        'yii\web\HttpException:403',
                        'yii\web\HttpException:404'
                    ],
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['info'],
                    'categories' => ['ssl_order_status'],
                    'logFile' => '@runtime/logs/ssl_order_status.log',
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['info'],
                    'categories' => ['ssl_csr'],
                    'logFile' => '@runtime/logs/ssl_csr.log',
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['info'],
                    'categories' => ['ssl_order'],
                    'logFile' => '@runtime/logs/ssl_order.log',
                ]
            ],
        ],
        'db' => $db,
        'i18n' => [
            'translations' => [
                'app*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@app/messages',
                    'sourceLanguage' => 'en',
                    'fileMap' => [
                        'app' => 'app.php',
                        'app/superadmin' => 'superadmin.php',
                    ],
                ],
            ],
        ],
    ],
    'params' => $params,
    'aliases' => [
        '@components' => '@app/components/',
        '@superadmin' => '@app/modules/superadmin/',
        '@libs' => '@app/libs/',
        '@webroot' => '@app/../web/',
    ],
    /*
    'controllerMap' => [
        'fixture' => [ // Fixture generation command line.
            'class' => 'yii\faker\FixtureController',
        ],
    ],
    */
];

if (!empty($params['devEmail'])) {
    $config['components']['log']['targets'][] = [
        'class' => 'yii\log\EmailTarget',
        'mailer' => 'mailerSwift',
        'levels' => ['error'],
        'message' => [
            'from' => ['noreply@getyourpanel.com'],
            'to' =>  $params['devEmail'],
            'subject' => 'Error console',
        ],
        'except' => [
            'yii\web\HttpException:403',
            'yii\web\HttpException:404'
        ],
    ];
}

if (!empty($params['failsEmail'])) {
    $config['components']['log']['targets'][] = [
        'class' => 'yii\log\EmailTarget',
        'mailer' => 'mailerSwift',
        'levels' => ['info'],
        'message' => [
            'from' => ['noreply@getyourpanel.com'],
            'to' =>  $params['failsEmail'],
            'subject' => 'Fail console',
        ],
        'categories' => ['order'],
    ];
}

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;

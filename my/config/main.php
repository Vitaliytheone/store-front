<?php

$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    file_exists(__DIR__ . '/../../common/config/params-local.php') ? require(__DIR__ . '/../../common/config/params-local.php') : [],
    require(__DIR__ . '/params.php'),
    file_exists(__DIR__ . '/params-local.php') ? require(__DIR__ . '/params-local.php') : []
);

$db = array_merge(
    require(__DIR__ . '/db.php'),
    file_exists(__DIR__ . '/db-local.php') ? require(__DIR__ . '/db-local.php') : []
);

$routes = require(__DIR__ . '/routes.php');

$config = [
    'id' => 'app-my',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'debug'],
    'controllerNamespace' => 'my\controllers',
    'language' => 'en-US',
    'components' => [
        'mailer' => [
            'class' => 'my\components\mailer\mailgun\Mailer',
            'key' => $params['mailgun.key'],
            'domain' => $params['mailgun.domain'],
            'viewPath' => '@my/mail/views',
        ],
        'mailerSwift' => [
            'class' => 'yii\swiftmailer\Mailer',
            // раскомментировать если использовать smtp отправку и наоборот
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => $params['swift.host'], //вставляем имя или адрес почтового сервера
                'username' => $params['swift.username'],
                'password' => $params['swift.password'],
                'port' => $params['swift.port'],
            ],
            'viewPath' => '@my/mail/views',
        ],
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '323y&^zcfqy)+w-f=f)^wfik^7vb=6rcj13$jo!e9npmbxi(l!i532',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'common\models\panels\Auth',
            'enableAutoLogin' => true,
            'loginUrl' => ['/signin'],
            'on afterLogin' => function($event) {
                /**
                 * @var $user \common\models\panels\Auth
                 */
                $user = Yii::$app->user->identity;

                if (!$user->isSuperadminAuth) {
                    $user->updateAttributes([
                        'auth_ip' => Yii::$app->getRequest()->getUserIP(),
                        'auth_date' => time()
                    ]);
                }
            },
            'on beforeLogout' => function($event) {
                /**
                 * @var \common\models\panels\Auth $user;
                 */
                $user = Yii::$app->user->identity;

                $hash = \my\helpers\UserHelper::getHash();

                $user->clearAuthKey($hash);
            },
            'authTimeout' => 3600 * 24 * 30,
            'identityCookie' => [
                'name' => '_identity_user',
                'httpOnly' => true
            ],
        ],
        'superadmin' => [
            'class' => 'my\components\User',
            'identityClass' => 'common\models\panels\SuperAdmin',
            'enableAutoLogin' => true,
            'loginUrl' => ['/' . $params['superadminUrl']],
            'idParam' => '__superadmin_id',
            'authTimeoutParam' => '__superadmin_expire',
            'absoluteAuthTimeoutParam' => '__superadmin_absoluteExpire',
            'returnUrlParam' => '__superadmin_returnUrl',
            'identityCookie' => [
                'name' => '_identity_superadmin',
                'httpOnly' => true
            ],
            'authTimeout' => 3600 * 24 * 30,
            'on afterLogin' => function($event) {
                Yii::$app->superadmin->identity->updateAttributes([
                    'last_ip' => Yii::$app->getRequest()->getUserIP(),
                    'last_login' => time()
                ]);
            }
        ],
        'formatter' => [
            'class' => 'my\components\Formatter',
            'dateFormat' => 'php:d.m.Y',
            'datetimeFormat' => 'php:d.m.Y H:i:s',
            'timeFormat' => 'php:H:i:s',
            'currencyCode' => '$'
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error'],
                    'except' => [
                        'yii\web\HttpException:400',
                        'yii\web\HttpException:403',
                        'yii\web\HttpException:404',
                        'yii\i18n\PhpMessageSource::loadMessages',
                        'yii\i18n\PhpMessageSource::loadFallbackMessages'
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
        'db' => $db['db'],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => false,
            'showScriptName' => false,
            'rules' => $routes,
            'routeParam' => 'route',
        ],
        'reCaptcha' => [
            'name' => 'reCaptcha',
            'class' => 'himiklab\yii2\recaptcha\ReCaptcha',
            'siteKey' => $params['reCaptcha.siteKey'],
            'secret' => $params['reCaptcha.secret'],
        ],
        'assetManager' => [
            'bundles' => [
                'yii\bootstrap\BootstrapAsset' => [
                    'css' => [],
                ],
                'yii\bootstrap\BootstrapPluginAsset' => [
                    'js' => [],
                ],
                'yii\web\JqueryAsset' => [
                    'js' => []
                ],
                'yii\web\YiiAsset' => [
                    'depends' => [
                        'my\assets\JqueryAsset'
                    ]
                ],
            ],
            'appendTimestamp' => true
        ],
        'geoip' => ['class' => 'lysenkobv\GeoIP\GeoIP'],
        'i18n' => [
            'translations' => [
                'app*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@my/messages',
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
        '@bower' => '@vendor/bower-asset',
    ],
    'modules' => [
        $params['superadminUrl'] => [
            'class' => '\my\modules\superadmin\Module',
            'defaultRoute' => 'site'
        ],
        'supervisor' => [
            'class' => 'my\modules\supervisor\Supervisor',
        ],
        'debug' => [
            'class' => 'yii\debug\Module',
            'allowedIPs' => $params['debugIps']
        ]
    ],
];


if (!empty($params['devEmail'])) {
    $config['components']['log']['targets'][] = [
        'class' => 'yii\log\EmailTarget',
        'mailer' => 'mailerSwift',
        'levels' => ['error'],
        'message' => [
            'from' => ['yii2errorlog@13.uz'],
            'to' =>  $params['devEmail'],
            'subject' => 'Error ' . $_SERVER['HTTP_HOST'],
        ],
        'except' => [
            'yii\web\HttpException:400',
            'yii\web\HttpException:403',
            'yii\web\HttpException:404',
            'yii\i18n\PhpMessageSource::loadMessages',
            'yii\i18n\PhpMessageSource::loadFallbackMessages'
        ],
    ];
}

if (!empty($params['failsEmail'])) {
    $config['components']['log']['targets'][] = [
        'class' => 'yii\log\EmailTarget',
        'mailer' => 'mailerSwift',
        'levels' => ['info'],
        'message' => [
            'from' => ['yii2errorlog@13.uz'],
            'to' =>  $params['failsEmail'],
            'subject' => 'Fail ' . $_SERVER['HTTP_HOST'],
        ],
        'categories' => ['order'],
    ];
}

if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;

<?php
use yii\web\UrlNormalizer;

$db = array_merge(
    require_once(__DIR__ . '/db.php'),
    file_exists(__DIR__ . '/db-local.php') ? require(__DIR__ . '/db-local.php') : []
);

$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    file_exists(__DIR__ . '/../../common/config/params-local.php') ? require(__DIR__ . '/../../common/config/params-local.php') : [],
    require(__DIR__ . '/params.php'),
    file_exists(__DIR__ . '/params-local.php') ? require(__DIR__ . '/params-local.php') : []
);

$configDb = DB_CONFIG;

$routers = require(__DIR__ . '/routers.php');

$config = [
    'id' => 'app-gateway',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'gateway', 'view'],
    'controllerNamespace' => 'gateway\controllers',
    'sourceLanguage' => 'esperanto',
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-gateway',
            'cookieValidationKey' => 'uKJVjhPVYpKcAirTEKcgVrcau5ZVPV0d',
            'class'	=> \gateway\components\MyRequest::class,
            'parsers' => [
                'application/json' => 'yii\web\JsonParser'
            ],
        ],
        'devMailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'transport' => [
                'class' => 'Swift_MailTransport',
            ],
        ],
        'user' => [
            'class' => 'admin\components\CustomUser',
            'identityClass' => \common\models\gateways\Admins::class,
            'loginUrl' => '/admin',
            'enableSession' => true,
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-gateway', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the frontend
            'name' => 'advanced-gateway',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'normalizer' => [
                'class' => 'yii\web\UrlNormalizer',
                'action' => UrlNormalizer::ACTION_REDIRECT_PERMANENT,
                'normalizeTrailingSlash' => true,
                'collapseSlashes' => true,
            ],
            'rules' => $routers,
        ],
        'assetManager' => [
            'bundles' => [
                'yii\web\JqueryAsset' => [
                    'js' => []
                ],
                'yii\web\YiiAsset' => [
                    'depends' => [
                        'gateway\assets\JqueryAsset'
                    ]
                ],
            ],
            'appendTimestamp' => true,
        ],
        'i18n' => [
            'translations' => [
                'admin*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => dirname(__DIR__) . '/messages',
                    'sourceLanguage' => 'en',
                    'fileMap' => [
                        'admin' => 'admin.php',
                    ]
                ],
                'app*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => dirname(__DIR__) . '/messages',
                    'sourceLanguage' => 'en',
                    'fileMap' => [
                        'app' => 'app.php',
                    ]
                ],
            ],
        ],
        'view' => [
            'class' => 'gateway\components\View',
            'renderers' => [
                'twig' => [
                    'class' => 'common\components\twig\ViewRenderer',
                    'cachePath' => null,
                    'extension' => \gateway\components\twig\Extension::class,
                    'options' => [
                        'autoescape' => false
                    ],
                ],
            ],
        ],
        'gateway' => [
            'class' => 'common\components\gateways\GatewayComponent'
        ],
        'db' => $db['db'],
        'gatewayDb' => $db['gatewayDb'],
    ],
    'params' => $params,
    'modules' => [
        'admin' => [
            'class' => 'admin\Module',
            'defaultRoute' => 'site'
        ],
    ],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
    ],
];

if (!empty($params['devEmail'])) {
    $config['components']['log']['targets'][] = [
        'class' => 'yii\log\EmailTarget',
        'mailer' => 'devMailer',
        'levels' => ['error'],
        'message' => [
            'from' => ['noreply@getyourpanel.com'],
            'to' =>  $params['devEmail'],
            'subject' => 'Error ' . $_SERVER['HTTP_HOST'],
        ],
        'except' => [
            'yii\web\HttpException:400',
            'yii\web\HttpException:403',
            'yii\web\HttpException:404',
            'yii\i18n\PhpMessageSource::loadMessages',
            'yii\i18n\PhpMessageSource::loadFallbackMessages',
        ],
    ];
}


if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => $params['debugIps']
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];

// Mysql logger
//    $config['components']['log']['targets'][] = [
//        'class' => 'yii\log\FileTarget',
//        'categories' => ['yii\db\*'],
//        'logFile' => dirname(dirname(__DIR__)) . '/gateway/runtime/logs/sql.log',
//    ];

//  Debug logger
    $config['components']['log']['targets'][] = [
        'class' => 'yii\log\FileTarget',
        'levels' => ['trace'],
        'categories' => ['my_debug'],
        'logVars' => [],
        'logFile' => dirname(dirname(__DIR__)) . '/gateway/runtime/logs/debug.log',
    ];

}

return $config;
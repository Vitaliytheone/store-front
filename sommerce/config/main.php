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

$configDb = $params['config.db'];

$routers = require(__DIR__ . '/routers.php');

$config = [
    'id' => 'app-sommerce',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'store'],
    'controllerNamespace' => 'sommerce\controllers',
    'sourceLanguage' => 'esperanto',
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-sommerce',
            'cookieValidationKey' => 'uKJVjhPVYpKcAirTEKcgVrcau5ZVPV0d',
            'class'	=> 'sommerce\components\MyRequest',
        ],
        'user' => [
            'class' => 'sommerce\modules\admin\components\CustomUser',
            'identityClass' => 'common\models\stores\StoreAdminAuth',
            'loginUrl' => '/admin',
            'enableSession' => true,
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-sommerce', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the frontend
            'name' => 'advanced-sommerce',
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
                        'sommerce\assets\JqueryAsset'
                    ]
                ],
            ],
        ],
        'i18n' => [
            'translations' => [
                'admin*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => dirname(__DIR__) . '/messages',
                    'fileMap' => [
                        'admin' => 'admin.php',
                    ],
                ],
                'app*' => [
                    'class' => 'sommerce\components\i18n\CustomMessageSource',
                    'basePath' => dirname(__DIR__) . '/messages',
                ],
            ],
        ],
        'view' => [
            'class' => 'common\components\View',
            'renderers' => [
                'twig' => [
                    'class' => 'common\components\twig\ViewRenderer',
                    //'cachePath' => $params['twig.cachePath'],
                    'cachePath' => null,
                    'options' => [
                        'autoescape' => false
                    ],
                ],
            ],
        ],
        'store' => [
            'class' => 'common\components\stores\StoreComponent'
        ],
        'db' => $db['db'],
        'storeDb' => $db['store_db'],
    ],
    'params' => $params,
    'modules' => [
        'admin' => [
            'class' => '\sommerce\modules\admin\Module',
            'defaultRoute' => 'site'
        ],
    ],
];


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

    $config['components']['log']['targets'][] = [
        'class' => 'yii\log\FileTarget',
        'categories' => ['yii\db\*'],
        'logFile' => dirname(dirname(__DIR__)) . '/sommerce/runtime/logs/sql.log',
    ];

}

return $config;
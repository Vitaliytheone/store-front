<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    file_exists(__DIR__ . '/../../common/config/params-local.php') ? require(__DIR__ . '/../../common/config/params-local.php') : [],
    require(__DIR__ . '/params.php'),
    file_exists(__DIR__ . '/params-local.php') ? require(__DIR__ . '/params-local.php') : []
);

$routers = require(__DIR__ . '/routers.php');

$config = [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'store'],
    'controllerNamespace' => 'frontend\controllers',
    'sourceLanguage' => 'esperanto',
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-frontend',
            'cookieValidationKey' => 'uKJVjhPVYpKcAirTEKcgVrcau5ZVPV0d',
            'class'	=> 'frontend\components\MyRequest',
        ],
        'user' => [
            'class' => 'yii\web\User',
            'identityClass' => 'common\models\stores\StoreAdmins',
            'loginUrl' => '/admin',
            'enableSession' => true,
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-frontend', 'httpOnly' => true],
            'on afterLogin' => ['common\models\stores\StoreAdmins', 'updateLoginData'],
            'on afterLogout' => ['common\models\stores\StoreAdmins', 'updateLogoutData'],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the frontend
            'name' => 'advanced-frontend',
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
            'rules' => $routers,
        ],
        'assetManager' => [
            'bundles' => [
                'yii\web\JqueryAsset' => [
                    'js' => []
                ],
                'yii\web\YiiAsset' => [
                    'depends' => [
                        'frontend\assets\JqueryAsset'
                    ]
                ],
            ],
        ],
        'store' => [
            'class' => 'common\components\stores\StoreComponent'
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
                    'class' => 'frontend\components\i18n\CustomMessageSource',
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
    ],
    'params' => $params,
    'modules' => [
        'admin' => [
            'class' => '\frontend\modules\admin\Module',
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
}

return $config;
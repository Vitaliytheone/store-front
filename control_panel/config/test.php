<?php
$params = require(__DIR__ . '/params.php');
$db = require(__DIR__ . '/test_db.php');

$params = array_merge(
    require(__DIR__ . '/params.php'),
    file_exists(__DIR__ . '/params-local.php') ? require(__DIR__ . '/params-local.php') : [],
    file_exists(__DIR__ . '/params-test.php') ? require(__DIR__ . '/params-test.php') : []
);

$db = array_merge(
    require(__DIR__ . '/test_db.php'),
    file_exists(__DIR__ . '/test_db-local.php') ? require(__DIR__ . '/test_db-local.php') : []
);

/**
 * Application configuration shared by all test types
 */
return [
    'id' => 'basic-tests',
    'basePath' => dirname(__DIR__),    
    'language' => 'en-US',
    'components' => [
        'db' => $db,
        'mailer' => [
            'useFileTransport' => true,
        ],
        'assetManager' => [            
            'basePath' => __DIR__ . '/../web/assets',
        ],
        'urlManager' => [
            'showScriptName' => true,
        ],
        'user' => [
            'identityClass' => 'common\models\sommerces\Users',
        ],
        'geoip' => ['class' => 'lysenkobv\GeoIP\GeoIP'],
        'request' => [
            'cookieValidationKey' => 'test',
            'enableCsrfValidation' => false,
            // but if you absolutely need it set cookie domain to localhost
            /*
            'csrfCookie' => [
                'domain' => 'localhost',
            ],
            */
        ],        
    ],
    'params' => $params,
    'aliases' => [
    ],
];

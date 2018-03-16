<?php


if (file_exists(__DIR__ . '/../basic/config/environments-local.php')) {
    require(__DIR__ . '/../basic/config/environments-local.php');
} else {
    require(__DIR__ . '/../basic/config/environments.php');
}

require('../basic/vendor/autoload.php');
require('../basic/vendor/yiisoft/yii2/Yii.php');

$config = yii\helpers\ArrayHelper::merge(
    require(__DIR__ . '/../basic/config/web.php'),
    file_exists(__DIR__ . '/../basic/config/web-local.php') ? require(__DIR__ . '/../basic/config/web-local.php') : []
);

(new yii\web\Application($config))->run();

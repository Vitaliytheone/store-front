<?php

if (file_exists(__DIR__ . '/../config/environments-local.php')) {
    require(__DIR__ . '/../config/environments-local.php');
} else {
    require(__DIR__ . '/../config/environments.php');
}

require(__DIR__ . '/../../vendor/autoload.php');
require(__DIR__ . '/../../vendor/yiisoft/yii2/Yii.php');

if (file_exists(__DIR__ . '/../../common/config/bootstrap-local.php')) {
    require(__DIR__ . '/../../common/config/bootstrap-local.php');
}

require(__DIR__ . '/../../common/config/bootstrap.php');
require(__DIR__ . '/../config/bootstrap.php');

require(__DIR__ . '/../../common/config/events.php');

$config = yii\helpers\ArrayHelper::merge(
    require(__DIR__ . '/../../common/config/main.php'),
    file_exists(__DIR__ . '/../../common/config/main-local.php') ? require(__DIR__ . '/../../common/config/main-local.php') : [],

    require(__DIR__ . '/../config/main.php'),
    file_exists(__DIR__ . '/../config/main-local.php') ? require(__DIR__ . '/../config/main-local.php') : []
);

(new yii\web\Application($config))->run();

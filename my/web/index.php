<?php

require(__DIR__ . '/../../vendor/autoload.php');
require(__DIR__ . '/../../vendor/yiisoft/yii2/Yii.php');
require(__DIR__ . '/../../common/config/bootstrap.php');
require(__DIR__ . '/../config/bootstrap.php');


if (file_exists(__DIR__ . '/../../my/config/environments-local.php')) {
    require(__DIR__ . '/../../my/config/environments-local.php');
} else {
    require(__DIR__ . '/../../my/config/environments.php');
}

$config = yii\helpers\ArrayHelper::merge(
    require(__DIR__ . '/../../my/config/web.php'),
    file_exists(__DIR__ . '/../../my/config/web-local.php') ?
        require(__DIR__ . '/../../my/config/web-local.php') :
        []
);

(new yii\web\Application($config))->run();

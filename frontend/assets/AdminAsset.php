<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main admin frontend application asset bundle.
 */
class AdminAsset extends AssetBundle
{
    public $basePath = '@webroot';

    public $baseUrl = '@web';

    public $css = [
        'css/admin/fonts.css',
        'css/admin/main.css',
    ];

    public $js = [
        'js/libs/underscore.js',
        'js/libs/popper.js',
        'js/libs/bootstrap.js',
        'js/main.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
    ];
}

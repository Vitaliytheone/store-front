<?php

namespace sommerce\assets;

use yii\web\AssetBundle;

/**
 * Main admin sommerce application asset bundle.
 */
class AdminAsset extends AssetBundle
{
    public $basePath = '@webroot';

    public $baseUrl = '@web';

    public $css = [
        'css/admin/fonts.css',
        'css/admin/main.css',
        'css/admin/styles.css',
        'css/admin/modal-loader.css',
    ];

    public $js = [
        'js/libs/underscore.js',
        'js/libs/popper.js',
        'js/libs/bootstrap.js',
        'js/libs/notification/toastr.js',
        'js/main.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'sommerce\assets\SommernoteAsset',
    ];
}

<?php

namespace sommerce\assets;

use yii\web\AssetBundle;

/**
 * AdminCustomAsset assets
 */
class AdminCustomAsset extends AssetBundle
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
        'js/main.js',
    ];

    public $depends = [
        'sommerce\assets\AdminCustomJsAsset',
        'yii\web\YiiAsset',
    ];
}

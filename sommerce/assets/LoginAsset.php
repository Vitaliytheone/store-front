<?php

namespace sommerce\assets;

use yii\web\AssetBundle;

/**
 * Main admin login page frontend application asset bundle.
 */
class LoginAsset extends AssetBundle
{
    public $basePath = '@webroot';

    public $baseUrl = '@web';

    public $css = [
        'css/admin/fonts.css',
        'css/admin/main.css',
        'css/admin/styles.css',
    ];

    public $depends = [
        'yii\web\YiiAsset',
    ];
}

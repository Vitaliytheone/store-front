<?php

namespace sommerce\assets;

use yii\web\AssetBundle;

/**
 * AdminCustomJsAsset assets
 */
class AdminCustomJsAsset extends AssetBundle
{
    public $sourcePath = '@webroot/js/libs/admin_custom/';

    public $js = [
        'main.js',
        'main.init.js',
    ];
}

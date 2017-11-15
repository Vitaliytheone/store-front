<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * admin/orders page asset bundle.
 */
class OrdersAsset extends AssetBundle
{
    public $basePath = '@webroot';

    public $baseUrl = '@web';

    public $css = [];

    public $js = [
        'js/libs/clipboard/clipboard.js',
    ];

    public $depends = [
        'frontend\assets\AdminAsset',
    ];
}

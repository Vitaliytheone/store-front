<?php

namespace sommerce\assets;

use yii\web\AssetBundle;

/**
 * Class AdminProductsAssets
 * @package sommerce\assets
 */
class AdminProductsAssets extends AssetBundle
{
    public $basePath = '@webroot/react_apps/products/';

    public $baseUrl = '@web/react_apps/products/';

    public $css = [
        'https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css',
        'static/css/main.css',
    ];

    public $js = [
        'static/js/main.js'
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'sommerce\assets\AdminAsset',
        'sommerce\assets\ReactAsset'
    ];
}
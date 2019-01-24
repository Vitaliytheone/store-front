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
        'static/css/main.css',
    ];

    public $js = [
        'static/js/main.js'
    ];

    public $depends = [
        'sommerce\assets\ProductsAsset',
    ];
}
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
        'css/admin/spectrum/spectrum.css',
        'https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.11/summernote-bs4.css',
        'static/css/main.css',
    ];

    public $js = [
        'js/libs/summernote/summernote-bs4.js',
        'static/js/main.js'
    ];

    public $depends = [
        'sommerce\assets\AdminAsset',
    ];
}
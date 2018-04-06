<?php

namespace sommerce\assets;

use yii\web\AssetBundle;

/**
 * /admin/products page asset bundle.
 */
class ProductsAsset extends AssetBundle
{
    public $basePath = '@webroot';

    public $baseUrl = '@web';

    public $css = [
        'css/admin/spectrum/spectrum.css',
        'css/admin/spectrum/spectrum-custom.css',
    ];

    public $js = [
        'js/libs/jquery-ui.js',
        'js/libs/summernote/summernote-bs4.js',
        'js/libs/spectrum/spectrum.js',
    ];

    public $depends = [
        'sommerce\assets\AdminAsset',
    ];
}

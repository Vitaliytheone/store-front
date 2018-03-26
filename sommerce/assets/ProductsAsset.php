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

    public $css = [];

    public $js = [
        'js/libs/jquery-ui.js',
        'js/libs/nestable/nestable.lib.js',
        'js/libs/nestable/nestable-script.js',
        'js/libs/summernote/summernote-bs4.js',
    ];

    public $depends = [
        'sommerce\assets\AdminAsset',
    ];
}

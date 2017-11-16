<?php

namespace frontend\assets;

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

        'js/in_dev/products.js', // TODO:: Delete scripts after the main script developing is finished

    ];

    public $depends = [
        'frontend\assets\AdminAsset',
    ];
}

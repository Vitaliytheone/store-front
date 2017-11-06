<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Admin-Products page asset bundle.
 */
class ProductsAsset extends AssetBundle
{
    public $basePath = '@webroot';

    public $baseUrl = '@web';

    public $css = [];

    public $js = [
        'js/libs/summernote/summernote-bs4.js',
        'js/libs/summernote/summernote.init.js',
        'js/libs/jquery-ui.js',
        'js/libs/nestable/nestable.lib.js',
        'js/libs/nestable/nestable-script.js',
        'js/libs/notification/toastr.js',
        'js/libs/notification/toastr.init.js',
    ];

    public $depends = [];
}

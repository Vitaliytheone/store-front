<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * /admin/products page asset bundle.
 */
class PagesAsset extends AssetBundle
{
    public $basePath = '@webroot';

    public $baseUrl = '@web';

    public $css = [];

    public $js = [
        'js/libs/summernote/summernote-bs4.js',
        'js/in_dev/pages.js', // TODO:: Delete scripts after the main script developing is finished
    ];

    public $depends = [
        'frontend\assets\AdminAsset',
    ];
}

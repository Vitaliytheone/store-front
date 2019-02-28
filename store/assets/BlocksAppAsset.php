<?php

namespace store\assets;

use yii\web\AssetBundle;

/**
 * Class BlocksAppAsset
 * @package store\assets
 */
class BlocksAppAsset extends AssetBundle
{
    public $basePath = '@webroot/react_apps/blocks/';

    public $baseUrl = '@web/react_apps/blocks/';

    public $css = [
        'static/css/main.css',
        'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.6.0/slick.min.css',
        'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.6.0/slick-theme.min.css',
    ];

    public $js = [
        'static/js/main.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'store\assets\ReactAsset',
    ];
}
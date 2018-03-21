<?php

namespace my\assets;

use yii\web\AssetBundle;

/**
 * Class BootstrapSelectAsset
 * @package my\assets
 */
class BootstrapAsset extends AssetBundle
{
    public $basePath = '@webroot/themes/';
    public $baseUrl = '@web/themes/';

    public $css = [
        'css/bootstrap.min.css',
    ];

    public $js = [
        'js/bootstrap.min.js',
    ];

    public $depends = [
        'my\assets\YiiAsset',
    ];
}
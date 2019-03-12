<?php

namespace control_panel\assets;

use yii\web\AssetBundle;

/**
 * Class BootstrapSelectAsset
 * @package control_panel\assets
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
        'control_panel\assets\YiiAsset',
    ];
}
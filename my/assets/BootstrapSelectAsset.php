<?php

namespace my\assets;

use yii\web\AssetBundle;

/**
 * Class BootstrapSelectAsset
 * @package my\assets
 */
class BootstrapSelectAsset extends AssetBundle
{
    public $basePath = '@webroot/themes/';
    public $baseUrl = '@web/themes/';

    public $js = [
        'js/libs/bootstrap_select/bootstrap-select.js',
    ];

    public $css = [
        'css/bootstrap-select.css',
    ];

    public $depends = [
        'my\assets\YiiAsset',
    ];
}
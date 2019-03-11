<?php

namespace control_panel\assets;

use yii\web\AssetBundle;

/**
 * Class JqueryAsset
 * @package control_panel\assets
 */
class JqueryAsset extends AssetBundle
{
    public $basePath = '@webroot/themes/';
    public $baseUrl = '@web/themes/';

    public $js = [
        'js/jquery.min.js',
    ];
}

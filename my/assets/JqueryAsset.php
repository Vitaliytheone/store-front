<?php

namespace my\assets;

use yii\web\AssetBundle;

/**
 * Class JqueryAsset
 * @package my\assets
 */
class JqueryAsset extends AssetBundle
{
    public $basePath = '@webroot/themes/';
    public $baseUrl = '@web/themes/';

    public $js = [
        'js/jquery.min.js',
    ];
}

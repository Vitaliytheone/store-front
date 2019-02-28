<?php

namespace store\assets;

use yii\web\AssetBundle;

/**
 * Class JqueryAsset
 * @package app\assets
 */
class JqueryAsset extends AssetBundle
{
    public $sourcePath = '@webroot/js/libs/';

    public $js = [
        'jquery.js',
    ];
}

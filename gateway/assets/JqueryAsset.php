<?php

namespace gateway\assets;

use yii\web\AssetBundle;

/**
 * Class JqueryAsset
 * @package gateway\assets
 */
class JqueryAsset extends AssetBundle
{
    public $sourcePath = '@webroot/js/libs/';

    public $js = [
        'jquery.js',
    ];
}

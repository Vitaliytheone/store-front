<?php

namespace my\assets;

use yii\web\AssetBundle;

/**
 * UnderscoreAsset frontend application asset bundle.
 */
class UnderscoreAsset extends AssetBundle
{
    public $sourcePath = '@node_modules/underscore';

    public $js = [
        'underscore-min.js',
    ];
}
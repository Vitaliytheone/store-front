<?php
namespace sommerce\assets;

use yii\web\AssetBundle;

/**
 * Rating assets
 */
class RatingAsset extends AssetBundle
{
    public $sourcePath = '@webroot/js/libs/';

    public $js = [
        'rating.lib.js',
    ];
}

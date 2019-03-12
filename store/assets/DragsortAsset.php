<?php
namespace store\assets;

use yii\web\AssetBundle;

/**
 * Dragsort assets
 */
class DragsortAsset extends AssetBundle
{
    public $sourcePath = '@webroot/js/libs/';

    public $js = [
        'dragsort.js',
    ];
}

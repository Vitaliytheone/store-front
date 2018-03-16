<?php

namespace my\assets;

use yii\web\AssetBundle;

/**
 * Class YiiAsset
 * @package my\assets
 */
class YiiAsset extends AssetBundle
{
    public $sourcePath = '@yii/assets';

    public $js = [
        'yii.js',
    ];

    public $depends = [
        'my\assets\JqueryAsset',
    ];
}

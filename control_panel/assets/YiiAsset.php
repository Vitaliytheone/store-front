<?php

namespace control_panel\assets;

use yii\web\AssetBundle;

/**
 * Class YiiAsset
 * @package control_panel\assets
 */
class YiiAsset extends AssetBundle
{
    public $sourcePath = '@yii/assets';

    public $js = [
        'yii.js',
    ];

    public $depends = [
        'control_panel\assets\JqueryAsset',
    ];
}

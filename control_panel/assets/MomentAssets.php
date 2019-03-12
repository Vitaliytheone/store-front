<?php

namespace control_panel\assets;

use yii\web\AssetBundle;

/**
 * Class MomentAssets
 * @package control_panel\assets
 */
class MomentAssets extends AssetBundle
{
    public $js = [
        'https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.6/moment.min.js',
    ];

    public $depends = [
        'control_panel\assets\YiiAsset',
    ];
}

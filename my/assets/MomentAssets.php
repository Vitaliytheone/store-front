<?php

namespace my\assets;

use yii\web\AssetBundle;

/**
 * Class MomentAssets
 * @package my\assets
 */
class MomentAssets extends AssetBundle
{
    public $js = [
        'https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.6/moment.min.js',
    ];

    public $depends = [
        'my\assets\YiiAsset',
    ];
}

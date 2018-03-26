<?php

namespace my\assets;

use yii\web\AssetBundle;

/**
 * Class HighChartsAssets
 * @package my\assets
 */
class HighChartsAssets extends AssetBundle
{
    public $js = [
        'https://code.highcharts.com/highcharts.src.js',
        'https://code.highcharts.com/highcharts-more.js',
    ];

    public $depends = [
        'my\assets\YiiAsset',
    ];
}

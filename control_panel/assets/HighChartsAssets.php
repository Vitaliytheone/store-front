<?php

namespace control_panel\assets;

use yii\web\AssetBundle;

/**
 * Class HighChartsAssets
 * @package control_panel\assets
 */
class HighChartsAssets extends AssetBundle
{
    public $js = [
        'https://code.highcharts.com/highcharts.src.js',
        'https://code.highcharts.com/highcharts-more.js',
    ];

    public $depends = [
        'control_panel\assets\YiiAsset',
    ];
}

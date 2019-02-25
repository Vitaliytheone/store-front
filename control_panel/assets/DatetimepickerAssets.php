<?php

namespace control_panel\assets;

use yii\web\AssetBundle;

/**
 * Class DatetimepickerAssets
 * @package control_panel\assets
 */
class DatetimepickerAssets extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $js = [
        '/themes/js/libs/datetimepicker/datetimepicker.js',
    ];

    public $css = [
        'https://cdn.rawgit.com/Eonasdan/bootstrap-datetimepicker/e8bddc60e73c1ec2475f827be36e1957af72e2ea/build/css/bootstrap-datetimepicker.css',
    ];

    public $depends = [
        'control_panel\assets\YiiAsset',
        'control_panel\assets\MomentAssets',
    ];
}

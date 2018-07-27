<?php

namespace my\assets;

use yii\web\AssetBundle;

/**
 * Class DatetimepickerAssets
 * @package my\assets
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
        'https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.0.0-alpha14/css/tempusdominus-bootstrap-4.min.css',
    ];

    public $depends = [
        'my\assets\YiiAsset',
        'my\assets\MomentAssets',
    ];
}

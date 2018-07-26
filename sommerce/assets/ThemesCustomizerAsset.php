<?php

namespace sommerce\assets;

use yii\web\AssetBundle;

/**
 * Class BlocksAppAsset
 * @package sommerce\assets
 */
class ThemesCustomizerAsset extends AssetBundle
{
    public $basePath = '@webroot/react_apps/themes_customizer/';

    public $baseUrl = '@web/react_apps/themes_customizer/';

    public $css = [
        'https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css',
        'static/css/main.css'
    ];

    public $js = [
        'static/js/main.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'sommerce\assets\ReactAsset',
    ];
}
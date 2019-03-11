<?php

namespace sommerce\assets;

use yii\web\AssetBundle;

/**
 * Class PagesAppAsset
 * @package sommerce\assets
 */
class PagesAppAsset extends AssetBundle
{
    public $basePath = '@webroot/react_apps/pages/';

    public $baseUrl = '@web/react_apps/pages/';

    public $css = [
        'static/css/main.css',
    ];

    public $js = [
        'static/js/main.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'sommerce\assets\ReactAsset',
    ];
}
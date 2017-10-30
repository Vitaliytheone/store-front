<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Metronic assets
 */
class MetronicAsset extends AssetBundle
{
    public $basePath = '@webroot';

    public $baseUrl = '@web';

    public $js = [
        'js/libs/metronic/app.js',
        'js/libs/metronic/util.js',
        'js/libs/metronic/components/general/animate.js',
        'js/libs/metronic/components/general/datatable.js',
        'js/libs/metronic/components/general/dropdown.js',
        'js/libs/metronic/components/general/header.js',
        'js/libs/metronic/components/general/menu.js',
        'js/libs/metronic/components/general/offcanvas.js',
        'js/libs/metronic/components/general/toggle.js',
        'js/libs/metronic/layout.js',
        'js/libs/metronic/components/base/blockui.js',
        'js/libs/metronic/components/base/dropdown.js',
    ];
}

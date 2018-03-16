<?php

namespace sommerce\assets;

use yii\web\AssetBundle;

/**
 * Metronic assets
 */
class MetronicAsset extends AssetBundle
{
    public $sourcePath = '@webroot/js/libs/';

    public $js = [
        'metronic/app.js',
        'metronic/util.js',
        'metronic/components/general/animate.js',
        'metronic/components/general/datatable.js',
        'metronic/components/general/dropdown.js',
        'metronic/components/general/header.js',
        'metronic/components/general/menu.js',
        'metronic/components/general/offcanvas.js',
        'metronic/components/general/toggle.js',
        'metronic/layout.js',
        'metronic/components/base/blockui.js',
        'metronic/components/base/dropdown.js',
    ];
}

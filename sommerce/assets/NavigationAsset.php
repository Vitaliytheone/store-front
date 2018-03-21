<?php

namespace sommerce\assets;

use yii\web\AssetBundle;

/**
 * /admin/settings/navigation page asset bundle.
 */
class NavigationAsset extends AssetBundle
{
    public $basePath = '@webroot';

    public $baseUrl = '@web';

    public $css = [];

    public $js = [
        'js/libs/nestable/nestable.lib.js',
    ];

    public $depends = [
        'sommerce\assets\AdminAsset',
    ];
}

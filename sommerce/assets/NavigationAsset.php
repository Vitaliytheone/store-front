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
        'js/in_dev/navigation.js', // TODO:: Delete scripts after the main script developing is finished
    ];

    public $depends = [
        'sommerce\assets\AdminAsset',
    ];
}

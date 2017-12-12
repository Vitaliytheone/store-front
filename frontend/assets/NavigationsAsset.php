<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * /admin/settings/navigations page asset bundle.
 */
class NavigationsAsset extends AssetBundle
{
    public $basePath = '@webroot';

    public $baseUrl = '@web';

    public $css = [];

    public $js = [
        'js/libs/nestable/nestable.lib.js',

        'js/in_dev/navigations.js', // TODO:: Delete scripts after the main script developing is finished
    ];

    public $depends = [
        'frontend\assets\AdminAsset',
    ];
}

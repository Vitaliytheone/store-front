<?php

namespace sommerce\assets;

use yii\web\AssetBundle;

/**
 * /admin/products page asset bundle.
 */
class PagesAsset extends AssetBundle
{
    public $basePath = '@webroot';

    public $baseUrl = '@web';

    public $css = [];

    public $js = [];

    public $depends = [
        'sommerce\assets\AdminAsset',
    ];
}

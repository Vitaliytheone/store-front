<?php

namespace sommerce\assets;

use yii\web\AssetBundle;

/**
 * /admin/settings/navigation page asset bundle.
 *
 * Class NavigationPaymentAsset
 * @package sommerce\assets
 */
class NavigationPaymentAsset extends AssetBundle
{
    public $basePath = '@webroot';

    public $baseUrl = '@web';

    public $css = [
        'css/admin/styles.css',
    ];

    public $js = [
        'js/libs/nestable/nestable.lib.js',
    ];

    public $depends = [
        'sommerce\assets\AdminAsset',
    ];
}

<?php
namespace gateway\assets;

use yii\web\AssetBundle;

/**
 * Class FilesAsset
 * @package gateway\assets
 */
class FilesAsset extends AssetBundle
{
    public $basePath = '@webroot';

    public $baseUrl = '@web';

    public $css = [];

    public $js = [];

    public $depends = [
        'gateway\assets\AdminAsset',
        'gateway\assets\CodemirrorAsset',
    ];
}

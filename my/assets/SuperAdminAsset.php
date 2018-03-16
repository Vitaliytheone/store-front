<?php
namespace my\assets;

use yii\web\AssetBundle;

class SuperAdminAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
        'themes/css/superadmin/bootstrap.min.css',
        'themes/css/font-awesome.min.css',
        'themes/css/superadmin/superadmin.css',
    ];

    public $js = [
        'themes/js/superadmin/tether.min.js',
        'themes/js/superadmin/bootstrap.min.js',
        'themes/js/superadmin/ie10-viewport-bug-workaround.js',
        'themes/js/script.js',
    ];

    public $depends = [
        'my\assets\YiiAsset',
        'my\assets\UnderscoreAsset',
        'my\assets\BootstrapSelectAsset',
        'my\assets\ClipboardAsset',
        'my\assets\TableSortAsset'
    ];
}
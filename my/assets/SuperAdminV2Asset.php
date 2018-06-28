<?php
namespace my\assets;

use yii\web\AssetBundle;

class SuperAdminV2Asset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
        'themes/css/superadmin/bootstrap.min.css',
        'themes/css/font-awesome.min.css',
        'themes/css/superadmin/style.css',
    ];

    public $js = [
        'https://code.jquery.com/jquery-3.2.1.min.js',
        'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js',
        'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js',
        'https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.20.1/moment.min.js',
        'https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.0.0-alpha14/js/tempusdominus-bootstrap-4.min.js',
        'themes/js/superadmin/main.js',
        'themes/js/script.js'
    ];

    public $depends = [
        'my\assets\YiiAsset',
        'my\assets\UnderscoreAsset',
        'my\assets\BootstrapSelectAsset',
        'my\assets\ClipboardAsset',
        'my\assets\TableSortAsset'
    ];
}
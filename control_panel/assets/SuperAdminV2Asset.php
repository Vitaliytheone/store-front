<?php
namespace control_panel\assets;

use yii\web\AssetBundle;

class SuperAdminV2Asset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
        'themes/css/font-awesome.min.css',
        'https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.0.0-alpha14/css/tempusdominus-bootstrap-4.min.css',
        'https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css',
        'themes/css/superadmin/style.css',
        'themes/css/superadmin/superadminV2.css',
    ];

    public $js = [
        'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js',
        'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js',
        'https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.20.1/moment.min.js',
        'https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.0.0-alpha14/js/tempusdominus-bootstrap-4.min.js',
        'https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js',
        'https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js',
        'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/js/bootstrap-select.min.js',
        'themes/js/superadmin/main.js',
        'themes/js/script.js'
    ];

    public $depends = [
        'control_panel\assets\YiiAsset',
        'control_panel\assets\UnderscoreAsset',
        'control_panel\assets\ClipboardAsset',
        'control_panel\assets\DatetimepickerAssets'
    ];
}

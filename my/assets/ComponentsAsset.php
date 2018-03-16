<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace my\assets;

use yii\helpers\ArrayHelper;
use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class ComponentsAsset extends AssetBundle
{
    public $basePath = '@webroot/themes/';
    public $baseUrl = '@web/themes/';

    public $css = [
        'css/bootstrap.min.css',
        'css/metisMenu.min.css',
        'css/sb-admin-2.css',
        'css/font-awesome.min.css',
        'css/custom.css',
    ];

    public $js = [
        '//oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js',
        '//oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js',
        'js/bootstrap.min.js',
        'js/metisMenu.min.js',
        'js/sb-admin-2.js',
    ];

    public $depends = [
        'my\assets\YiiAsset',
        'my\assets\UnderscoreAsset',
        'my\assets\BootstrapAsset',
        'my\assets\BootstrapSelectAsset',
    ];
}

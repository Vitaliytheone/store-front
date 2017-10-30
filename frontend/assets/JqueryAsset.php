<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Class JqueryAsset
 * @package app\assets
 */
class JqueryAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $js = [
        '/js/libs/jquery.js',
    ];
}

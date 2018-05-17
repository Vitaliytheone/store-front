<?php

namespace sommerce\assets;

use yii\web\AssetBundle;

class ReactAsset extends AssetBundle
{
    public $basePath = '@webroot';

    public $baseUrl = '@web';

    public $css = [
        'css/admin/fonts.css',
        'css/admin/main.css',
    ];

    public $js = [];

    public $depends = [];
}

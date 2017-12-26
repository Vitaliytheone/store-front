<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Class ThemesAsset
 * @package frontend\assets
 */
class ThemesAsset extends AssetBundle
{
    public $basePath = '@webroot';

    public $baseUrl = '@web';

    public $css = [];

    public $js = [
        'js/libs/codemirror/codemirror.lib.js',
        'js/libs/codemirror/codemirror-compressed.lib.js',
        'js/libs/jstree/jstree.lib.js',
    ];

    public $depends = [
        'frontend\assets\AdminAsset',
    ];
}

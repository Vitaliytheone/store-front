<?php
namespace gateway\assets;

use yii\web\AssetBundle;

/**
 * Class ThemesAsset
 * @package gateway\assets
 */
class ThemesAsset extends AssetBundle
{
    public $basePath = '@webroot';

    public $baseUrl = '@web';

    public $css = [
        'js/libs/codemirror/vendor/codemirror/v4/codemirror.css',
        'js/libs/codemirror/vendor/codemirror/v4/foldgutter.css',
    ];

    public $js = [
        'js/libs/codemirror/vendor/codemirror/v4/codemirror.js',
        'js/libs/codemirror/vendor/codemirror/v4/xml.js',
        'js/libs/codemirror/vendor/codemirror/v4/css.js',
        'js/libs/codemirror/vendor/codemirror/v4/htmlmixed.js',
        'js/libs/codemirror/emmet.min.js',
        'js/libs/codemirror/codemirrorlibs.js',

        'js/libs/jstree/jstree.lib.js',
    ];

    public $depends = [
        'gateway\assets\AdminAsset',
    ];
}

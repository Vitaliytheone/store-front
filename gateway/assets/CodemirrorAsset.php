<?php
namespace gateway\assets;

use yii\web\AssetBundle;

/**
 * Class CodemirrorAsset
 * @package gateway\assets
 */
class CodemirrorAsset extends AssetBundle
{
    public $basePath = '@webroot';

    public $baseUrl = '@web';

    public $css = [
        'js/libs/codemirror/vendor/codemirror/v4/codemirror.css',
        'http://codemirror.net/addon/fold/foldgutter.css',
    ];

    public $js = [
        'js/libs/codemirror/vendor/codemirror/v4/codemirror.js',
        'js/libs/codemirror/vendor/codemirror/v4/xml.js',
        'js/libs/codemirror/vendor/codemirror/v4/css.js',
        'js/libs/codemirror/vendor/codemirror/v4/htmlmixed.js',
        'js/libs/codemirror/emmet.min.js',
        'js/libs/codemirror/codemirrorlibs.js',
        'http://codemirror.net/addon/fold/foldcode.js',
        'http://codemirror.net/addon/fold/foldgutter.js',
        'http://codemirror.net/addon/fold/brace-fold.js',
        'http://codemirror.net/addon/fold/xml-fold.js',
        'http://codemirror.net/addon/fold/indent-fold.js',
        'http://codemirror.net/addon/fold/markdown-fold.js',
        'http://codemirror.net/addon/fold/comment-fold.js',
        'http://codemirror.net/mode/javascript/javascript.js',
        'http://codemirror.net/mode/javascript/javascript.js',
        'http://codemirror.net/mode/htmlmixed/htmlmixed.js',
        'http://codemirror.net/mode/markdown/markdown.js'
    ];
}

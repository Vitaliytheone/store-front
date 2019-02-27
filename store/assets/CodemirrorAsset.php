<?php
namespace store\assets;

use yii\web\AssetBundle;

/**
 * Codemirror assets
 */
class CodemirrorAsset extends AssetBundle
{
    public $sourcePath = '@webroot/js/libs/codemirror/vendor/codemirror/v4/';

    public $js = [
        'codemirror.js',
        'xml.js',
        'css.js',
        'htmlmixed.js',
        'emmet.min.js',
        'http://codemirror.net/addon/fold/foldcode.js',
        'http://codemirror.net/addon/fold/foldgutter.js',
        'http://codemirror.net/addon/fold/brace-fold.js',
        'http://codemirror.net/addon/fold/xml-fold.js',
        'http://codemirror.net/addon/fold/indent-fold.js',
        'http://codemirror.net/addon/fold/markdown-fold.js',
        'http://codemirror.net/addon/fold/comment-fold.js',
        'http://codemirror.net/mode/javascript/javascript.js',
        'http://codemirror.net/mode/htmlmixed/htmlmixed.js',
        'http://codemirror.net/mode/markdown/markdown.js',
    ];

    public $css = [
        'codemirror.css',
        'http://codemirror.net/addon/fold/foldgutter.css',
    ];
}
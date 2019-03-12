<?php

namespace store\assets;

use yii\web\AssetBundle;

/**
 * Class SommernoteAsset
 * @package store\assets
 */
class SommernoteAsset extends AssetBundle
{
    public $basePath = '@webroot';

    public $baseUrl = '@web';

    public $js = [
        '/js/libs/summernote.js',
        '/js/libs/summernote-page.js',
        '/js/libs/summernote-term.js',
    ];

    public $css = [
        'css/admin/summernote.css',
    ];

    public $depends = [
        'store\assets\JqueryAsset',
        'store\assets\JqueryUiAsset',
    ];
}
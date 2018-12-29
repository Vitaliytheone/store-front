<?php

namespace sommerce\assets;

use yii\web\AssetBundle;

/**
 * Class SommernoteAsset
 * @package sommerce\assets
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
        'sommerce\assets\JqueryAsset',
        'sommerce\assets\JqueryUIAsset',
    ];
}
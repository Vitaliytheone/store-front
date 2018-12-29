<?php
namespace gateway\assets;

use yii\web\AssetBundle;

/**
 * Class JqueryUiAsset
 * @package gateway\assets
 */
class JqueryUiAsset extends AssetBundle
{
    public $sourcePath = '@webroot/js/libs/';

    public $js = [
        'jquery-ui.js',
    ];
}

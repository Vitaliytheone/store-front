<?php
namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Class JqueryUiAsset
 * @package app\assets
 */
class JqueryUiAsset extends AssetBundle
{
    public $sourcePath = '@webroot/js/libs/';

    public $js = [
        'jquery-ui.js',
    ];
}

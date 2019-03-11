<?php
namespace store\assets;

use yii\web\AssetBundle;

/**
 * Toastr assets
 */
class ToastrAsset extends AssetBundle
{
    public $sourcePath = '@webroot/js/libs/';

    public $js = [
        'toastr.js',
    ];
}

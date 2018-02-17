<?php
namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Textarea Autosize assets
 */
class TextareaAutosizeAsset extends AssetBundle
{
    public $sourcePath = '@webroot/js/libs/';

    public $js = [
        'textarea_autosize.js',
    ];
}

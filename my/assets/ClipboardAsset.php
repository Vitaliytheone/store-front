<?php

namespace my\assets;

use yii\web\AssetBundle;

/**
 * Class ClipboardAsset
 * @package my\assets
 */
class ClipboardAsset extends AssetBundle
{
    public $sourcePath = '@webroot/node_modules/clipboard/dist';

    public $js = [
        'clipboard.min.js',
    ];

    public $depends = [
        'my\assets\YiiAsset',
    ];
}
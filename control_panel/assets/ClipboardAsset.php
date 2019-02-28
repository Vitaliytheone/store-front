<?php

namespace control_panel\assets;

use yii\web\AssetBundle;

/**
 * Class ClipboardAsset
 * @package control_panel\assets
 */
class ClipboardAsset extends AssetBundle
{
    public $sourcePath = '@node_modules/clipboard/dist';

    public $js = [
        'clipboard.min.js',
    ];

    public $depends = [
        'control_panel\assets\YiiAsset',
    ];
}
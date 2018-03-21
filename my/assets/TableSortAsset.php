<?php

namespace my\assets;

use yii\web\AssetBundle;

/**
 * Class TableSortAsset
 * @package my\assets
 */
class TableSortAsset extends AssetBundle
{
    public $sourcePath = '@node_modules/tablesorter/dist';

    public $js = [
        'js/jquery.tablesorter.js',
    ];

    public $css = [
        'css/theme.bootstrap_4.min.css',
    ];

    public $depends = [
        'my\assets\YiiAsset',
    ];
}

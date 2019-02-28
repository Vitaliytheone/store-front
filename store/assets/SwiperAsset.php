<?php
namespace store\assets;

use yii\web\AssetBundle;

/**
 * Swiper assets
 */
class SwiperAsset extends AssetBundle
{
    public $sourcePath = '@webroot/js/libs/';

    public $js = [
        'swiper.js',
    ];
}

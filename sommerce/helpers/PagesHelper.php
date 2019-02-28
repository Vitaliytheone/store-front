<?php

namespace sommerce\helpers;

use common\models\sommerce\Pages;
use yii\helpers\ArrayHelper;

/**
 * Class FilesHelper
 * @package gateway\helpers
 */
class PagesHelper
{

    /**
     * @var array
     */
    public static $pages;

    /**
     * @return mixed
     */
    public static function getPages()
    {
        if (null === static::$pages) {
            static::$pages = ArrayHelper::index(Pages::find()->select([
                'id',
                'url',
                'seo_title',
                'seo_description',
                'seo_keywords',
                'twig',
                'created_at',
                'updated_at',
                'publish_at',
            ])->active()->asArray()->all(), 'url');
        }

        return static::$pages;
    }

    /**
     * Find page or return "Not found" exception
     * @param string $url
     * @return array
     */
    public static function getPage($url)
    {
        return ArrayHelper::getValue(static::getPages(), $url);
    }

}
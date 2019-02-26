<?php

namespace sommerce\helpers;

use common\helpers\ThemesHelper;
use common\models\store\Pages;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * Class FilesHelper
 * @package gateway\helpers
 */
class PagesHelper {

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
                'title',
                'seo_description',
                'seo_keywords',
                'is_draft',
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
     * @throws NotFoundHttpException
     */
    public static function getPage($url)
    {
        $page = ArrayHelper::getValue(static::getPages(), $url);

        if (!$page) {
            throw new NotFoundHttpException("Page by url '{$url}' not found");
        }
        return $page;
    }

    /**
     * Get current layout from file if exist
     * @param string $name
     * @return mixed|null
     * @throws NotFoundHttpException
     */
    public static function getLayout($name = 'layout.twig')
    {
        $layouts = file_get_contents(ThemesHelper::getView($name));

        if (empty($layouts)){
            $layouts = PageFilesHelper::getFileByName($name);
            $layouts = $layouts['content'];
        }

        return $layouts;
    }

}
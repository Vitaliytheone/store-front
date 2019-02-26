<?php

namespace sommerce\helpers;

use common\helpers\ThemesHelper;
use common\models\store\Pages;
use common\models\stores\Stores;
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
    public static function getFiles()
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
     * Find page or return exception
     * @param string $url
     * @return array
     * @throws NotFoundHttpException
     */
    public static function getPage($url)
    {
        $page = ArrayHelper::getValue(static::getFiles(), $url);

        if (!$page) {
            throw new NotFoundHttpException('Not found page by url '. $url);
        }
        return $page;
    }

    /**
     * Get current layout
     * @param string $name
     * @return mixed|null
     * @throws NotFoundHttpException
     */
    public static function getLayout($name = 'layout.twig')
    {
        $layouts = PageFilesHelper::getFileByName($name);

        if (empty($layouts)){
            $layouts = ThemesHelper::getView($name);
        }

        return $layouts;
    }

}
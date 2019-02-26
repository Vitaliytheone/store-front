<?php

namespace sommerce\helpers;

use common\models\store\PageFiles;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * Class FilesHelper
 * @package gateway\helpers
 */
class PageFilesHelper {

    /** @var array */
    public static $pageFiles;


    /**
     * @return mixed
     */
    public static function getFiles()
    {
        if (null === static::$pageFiles) {
            static::$pageFiles = ArrayHelper::index(PageFiles::find()->select([
                'id',
                'file_name',
                'file_type',
                'updated_at',
                'created_at',
                'publish_at',
                'content',
            ])->asArray()->all(), 'file_name');
        }

        return static::$pageFiles;
    }


    /**
     * @return mixed
     */
    public static function getFilesGroupByType()
    {
        $pageFileGroup = ArrayHelper::index(static::getFiles(), 'file_name', 'file_type');

        return $pageFileGroup;
    }

    /**
     * Get files by name
     * @param string $name
     * @return mixed
     * @throws NotFoundHttpException
     */
    public static function getFileByName($name)
    {
        $file = ArrayHelper::getValue(static::getFiles(), $name);

        if (empty($file)) {
            throw new NotFoundHttpException("File {$name} not found");
        }

        return $file;
    }

    /**
     * Generate link with version param
     * @param string $value
     * @return string
     * @throws NotFoundHttpException
     */
    public static function generateFileVersionLink($value)
    {
        $name = '';

        if (stripos($value, 'styles.css') !== false) {
            $name = 'styles.css';
        }
        if (stripos($value, 'scripts.js') !== false) {
            $name = 'scripts.js';
        }

        /** @var array $files */
        $file = self::getFileByName($name);

        return "/{$value}?v={$file['updated_at']}";
    }

}
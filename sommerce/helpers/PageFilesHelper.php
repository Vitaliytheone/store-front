<?php

namespace sommerce\helpers;

use common\models\sommerce\PageFiles;
use yii\helpers\ArrayHelper;

/**
 * Class FilesHelper
 * @package gateway\helpers
 */
class PageFilesHelper
{

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
            ])
                ->asArray()
                ->all(), 'file_name');
        }

        return static::$pageFiles;
    }


    /**
     * @return mixed
     */
    public static function getFilesGroupByType()
    {
        return ArrayHelper::index(static::getFiles(), 'file_name', 'file_type');
    }

    /**
     * Get files by name
     * @param string $name
     * @return mixed
     */
    public static function getFileByName($name)
    {
        return ArrayHelper::getValue(static::getFiles(), $name);
    }

    /**
     * Generate link with version param
     * @param string $value
     * @return string
     */
    public static function generateFileVersionLink($value): string
    {
        $value = ltrim($value, '/');
        $valueTrim = explode('/', $value);
        $valueTrim = $valueTrim[1] ?? $valueTrim[0];

        /** @var array $files */
        $file = self::getFileByName($valueTrim);

        return "/{$value}?v={$file['updated_at']}";
    }

}
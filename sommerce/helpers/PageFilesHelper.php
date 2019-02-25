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
    public static $page_files;

    /**
     * @return mixed
     */
    public static function getFiles()
    {

        if (null === static::$page_files) {
            static::$page_files = ArrayHelper::index(PageFiles::find()->select([
                'id',
                'file_name',
                'file_type',
                'updated_at',
                'created_at',
                'publish_at',
                'content',
            ])->asArray()->all(), 'file_name', 'file_type');
        }

        return static::$page_files;
    }

    /**
     * Get files by name
     * @param string $type
     * @param string $name
     * @return mixed
     * @throws NotFoundHttpException
     */
    public static function getFileByName($type, $name)
    {
        $file = ArrayHelper::getValue(static::getFiles(), [$type, $name]);
        if (empty($file)) {
            throw new NotFoundHttpException();
        }

        return $file;
    }

    /**
     * Get files by name
     * @param string $value
     * @return string
     * @throws NotFoundHttpException
     */
    public static function generateFileVersionLink($value)
    {
        $type = $name = '';

        if (stripos($value, 'styles.css') !== false) {
            $type = 'css';
            $name = 'styles.css';
        }
        if (stripos($value, 'scripts.js') !== false) {
            $type = 'js';
            $name = 'scripts.js';
        }

        /** @var array $files */
        $files = self::getFileByName($type, $name);

        return "/{$value}?v={$files['updated_at']}";
    }

}
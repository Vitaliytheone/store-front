<?php
namespace gateway\helpers;

use common\models\gateway\Files;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class FilesHelper
 * @package gateway\helpers
 */
class FilesHelper {

    /**
     * @var array
     */
    static $pages;

    /**
     * @return mixed
     */
    public static function getFiles()
    {
        if (null === static::$pages) {
            static::$pages = ArrayHelper::index(Files::find()->select([
                'id',
                'name',
                'url',
                'file_type',
                'is_default',
                'updated_at',
                'content',
            ])->active()->asArray()->all(), 'id', 'file_type');
        }

        return static::$pages;
    }

    /**
     * @param string $type
     * @param integer $id
     * @return mixed
     */
    public static function getFileById($type, $id)
    {
        return ArrayHelper::getValue(static::getFiles(), [$type, $id]);
    }

    /**
     * @param string $url
     * @return mixed
     */
    public static function getPage($url)
    {
        $pages = ArrayHelper::index((array)ArrayHelper::getValue(static::getFiles(), Files::FILE_TYPE_PAGE), 'url');
        return ArrayHelper::getValue($pages, $url);
    }

    /**
     * @return mixed|null
     */
    public static function getLayout()
    {
        $layouts = (array)ArrayHelper::getValue(static::getFiles(), Files::FILE_TYPE_LAYOUT);

        return !empty($layouts) ? array_shift($layouts) : null;
    }
}
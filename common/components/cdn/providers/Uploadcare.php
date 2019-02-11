<?php

namespace common\components\cdn\providers;

use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use common\components\cdn\BaseCdn;
use Uploadcare\Api;
use Uploadcare\File;

/**
 * Adapter class for Uploadcare CDN
 * @package common\components\cdn
 *
 * @property Api $_api
 * @property File $_file
 */
class Uploadcare extends BaseCdn
{
    private $_api;
    private $_file;

    public function __construct($settings = [])
    {
        $publicKey = ArrayHelper::getValue($settings, 'public_key');
        $secretKey = ArrayHelper::getValue($settings, 'secret_key');

        if (!$publicKey || !$secretKey) {
            throw new Exception('MESSAGE_BAD_CONFIG');
        }

        $this->_api = new Api($publicKey, $secretKey);
    }

    /**
     * @inheritdoc
     */
    public function uploadFromPath($filePath, $mime = null)
    {
        if (!file_exists($filePath)) {
            throw new Exception(self::MESSAGE_FILE_NOT_FOUND);
        }

        $this->_file = $this->_api->uploader->fromPath($filePath, $mime);
        $this->_file->store();

        return $this->_file->getUuid();
    }

    /**
     * @inheritdoc
     */
    public function getId($cdnUrl)
    {
        if (!$this->_file instanceof File) {
            $this->_file = $this->_api->getFile($cdnUrl);
        }

        return $this->_file->getUuid();
    }

    /**
     * @inheritdoc
     */
    public function getUrl($cdnId)
    {
        if (!$this->_file instanceof File) {
            $this->_file = $this->_api->getFile($cdnId);
        }

        return $this->_file->getUrl();
    }

    /**
     * @inheritdoc
     */
    public function delete($cdnId)
    {
        if (!$this->_file instanceof File) {
            $this->_file = $this->_api->getFile($cdnId);
        }

        return $this->_file->delete();
    }

    /**
     * @inheritdoc
     */
    public function store($fileId)
    {
        if (!$this->_file instanceof File) {
            $this->_file = $this->_api->getFile($fileId);
        }

        try {
            $this->_file->store(true);
        } catch (Exception $e) {
//            echo $e->getMessage()."\n";
//            echo $e->getTraceAsString()."\n";
            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    public function getScript()
    {
        return $this->_api->widget->getScriptSrc();
    }

    /**
     * @return string
     */
    public function getConfigCode()
    {
        $code = 'UPLOADCARE_PUBLIC_KEY = "' . $this->_api->getPublicKey() . '";';
        return $code;
    }

    /**
     * @return string
     */
    public function getScriptWithConfig()
    {
        return $this->_api->widget->getScriptTag();
    }

    /**
     * @return string
     */
    public function getWidget()
    {
        return $this->_api->widget->getInputTag('qs-file', ['data-multiple' => true, 'data-multiple-max' => Yii::$app->params['uploadFileLimit'],]);
    }

    /**
     * Get Files
     * @param $cdnId string CDN object `id`
     * @param bool $links if set return array of upload files [name, link]
     * @return array
     * @throws \Exception
     */
    public function getFiles($cdnId, $links = false)
    {
        $this->_file = $this->_api->getGroup($cdnId);

        $files = $this->_file->getFiles();
        Yii::debug($files, 'files RAW'); // todo del

        if ($links === true) {
            $result = [];
            foreach ($files as $file) {
                $result[] = [
                    'uuid' => $file->data['uuid'],
                    'name' => $file->data['original_filename'],
                    'link' => $file->data['original_file_url'],
                    'size' => round ($file->data['size'] / 1000, 2) . ' Kb',
                ];
            }
            Yii::debug($result, '$result array'); // todo del
            return $result;
        }

        return $files;
    }

    /**
     * Store group of uploaded files
     * @param $cdnId string CDN object `id`
     * @return bool
     * @throws \Exception
     */
    public function storeGroup($cdnId): bool
    {
        try {
            $this->_file = $this->_api->getGroup($cdnId);
            $this->_file->store();
        } catch (Exception $e) {
            echo $e->getMessage()."\n";
            echo $e->getTraceAsString()."\n";
            return false;
        }
        return true;
    }

    /**
     * Delete group of uploaded files
     * @param $cdnIds array CDN object `id`
     * @return bool
     * @throws \Exception
     */
    public function deleteGroup($cdnIds): bool
    {
        try {
            $result = $this->_api->deleteMultipleFiles($cdnIds);
            Yii::debug($result, '$result');
        } catch (Exception $e) {
            echo $e->getMessage()."\n";
            echo $e->getTraceAsString()."\n";
            return false;
        }

        return true;
    }

}
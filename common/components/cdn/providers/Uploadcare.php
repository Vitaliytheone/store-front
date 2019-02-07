<?php

namespace common\components\cdn\providers;

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
        $publicKey = ArrayHelper::getValue($settings, 'public_key', null);
        $secretKey = ArrayHelper::getValue($settings, 'secret_key', null);

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
        if (! $this->_file instanceof File) {
            $this->_file = $this->_api->getFile($cdnUrl);
        }

        return $this->_file->getUuid();
    }

    /**
     * @inheritdoc
     */
    public function getUrl($cdnId)
    {
        if (! $this->_file instanceof File) {
            $this->_file = $this->_api->getFile($cdnId);
        }

        return $this->_file->getUrl();
    }

    /**
     * @inheritdoc
     */
    public function delete($cdnId)
    {
        if (! $this->_file instanceof File) {
            $this->_file = $this->_api->getFile($cdnId);
        }

        return $this->_file->delete();
    }

    /**
     * @return
     */
    public function getScript()
    {
        return $this->_api->widget->getScriptSrc();
    }

    /**
     * @return
     */
    public function getConfigCode()
    {
        $code = 'UPLOADCARE_PUBLIC_KEY = "'.$this->_api->getPublicKey().'";';
        return $code;
    }

    /**
     * @return
     */
    public function getScriptWithConfig()
    {
        return $this->_api->widget->getScriptTag();
    }

    /**
     * @return
     */
    public function getWidget()
    {
        return $this->_api->widget->getInputTag('qs-file');
    }
}
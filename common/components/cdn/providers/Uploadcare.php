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
    public const FILE_SIZE = '5242880'; // max file size in byte

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
     * Store file in cdn
     * @param string $fileId
     * @throws \Exception
     */
    public function store($fileId)
    {
        if (!$this->_file instanceof File) {
            $this->_file = $this->_api->getFile($fileId);
        }

        $this->_file->store();
    }

    /**
     * Get link to script source
     * @return string
     */
    public function getScript(): string
    {
        return $this->_api->widget->getScriptSrc();
    }

    /**
     * Get public key code
     * @return string
     */
    public function getPublicKey(): string
    {
        return $this->_api->getPublicKey();
    }

    /**
     * Get generated input for widget with options
     * @param array $options
     * @return string
     */
    public function getWidget($options = []): string
    {
        return $this->_api->widget->getInputTag('qs-file', $options);
    }

    /**
     * Get Files from group
     * @param string $cdnId CDN object `id`
     * @param bool $links if FALSE return array of "Files objects"
     * @return array return array of upload files [uuid, name, link, size]
     * @throws \Exception
     */
    public function getFiles($cdnId, $links = false)
    {
        $this->_file = $this->_api->getGroup($cdnId);

        $files = $this->_file->getFiles();

        if ($links === true) {
            $result = [];
            foreach ($files as $file) {
                $result[] = [
                    'uuid' => $file->data['uuid'],
                    'name' => $file->data['original_filename'],
                    'link' => $file->data['original_file_url'],
                    'mime' => $file->data['mime_type'],
                    'size' => round($file->data['size'] / 1000, 1) . ' Kb',
                ];
            }

            return $result;
        }

        return $files;
    }

    /**
     * Store group of uploaded files
     * @param string $cdnId CDN object `id`
     * @return bool
     * @throws \Exception
     */
    public function storeGroup($cdnId): bool
    {
        try {
            $this->_file = $this->_api->getGroup($cdnId);
            $this->_file->store();
        } catch (Exception $e) {
            Yii::error($e->getMessage() . "\n" . $e->getTraceAsString() . "\n");
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
        } catch (Exception $e) {
            Yii::error($e->getMessage() . "\n" . $e->getTraceAsString() . "\n");
            return false;
        }

        return true;
    }

}
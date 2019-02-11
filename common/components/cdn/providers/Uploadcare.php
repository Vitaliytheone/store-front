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
    public function getScript(): string
    {
        return $this->_api->widget->getScriptSrc();
    }

    /**
     * Get public key code
     * @return string
     */
    public function getConfigCode(): string
    {
        $code = 'UPLOADCARE_PUBLIC_KEY = "' . $this->_api->getPublicKey() . '";';
        return $code;
    }

    /**
     * @return string
     */
    public function setMaxSize(): string
    {
        $script = <<< JS
            function fileSizeLimit(max) {
              return function(fileInfo) {
                if (fileInfo.size === null) {
                  return;
                }
                if (max && fileInfo.size > max) {
                  throw new Error("fileMaximumSize");
                }
              };
            }
            $(function() {
              $('[role=uploadcare-uploader]').each(function() {
                var input = $(this);
                if (!input.data('maxSize')) {
                  return;
                }
                var widget = uploadcare.Widget(input);
                widget.validators.push(input.data('maxSize'));
              });
            });
JS;

        return $script;
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
        return $this->_api->widget->getInputTag('qs-file', ['data-multiple' => true, 'data-multiple-max' => Yii::$app->params['uploadFileLimit'], 'data-max-size' => '524']);
    }

    /**
     * Get Files
     * @param string $cdnId CDN object `id`
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
                    'size' => round($file->data['size'] / 1000, 1) . ' Kb',
                ];
            }
            Yii::debug($result, '$result array'); // todo del
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
            echo $e->getMessage() . "\n";
            echo $e->getTraceAsString() . "\n";
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
            echo $e->getMessage() . "\n";
            echo $e->getTraceAsString() . "\n";
            return false;
        }

        return true;
    }

}
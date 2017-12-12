<?php

namespace frontend\modules\admin\models\forms;

use Yii;
use common\models\stores\Stores;
use yii\validators\FileValidator;
use yii\web\UploadedFile;
use common\components\cdn\Cdn;
use common\components\cdn\BaseCdn;


/**
 * Class EditStoreSettingsForm
 * @property UploadedFile $faviconFile
 * @property UploadedFile $logoFile
 * @package frontend\modules\admin\models\forms
 */
class EditStoreSettingsForm extends Stores
{
    public $faviconFile;
    public $logoFile;


    private static $_files = [
        'logoFile' => ['extensions' => 'png, jpg, gif',
            'maxSize' => 3000000,
            'mimeTypes' => [
                BaseCdn::MIME_JPEG,
                BaseCdn::MIME_PNG,
                BaseCdn::MIME_GIF
            ]
        ],
        'faviconFile' => ['extensions' => 'png, jpg, gif, ico',
            'maxSize' => 500000,
            'mimeTypes' => [
                BaseCdn::MIME_JPEG,
                BaseCdn::MIME_PNG,
                BaseCdn::MIME_GIF,
                BaseCdn::MIME_ICO
            ]
        ],
    ];

    /**
     * @inheritdoc
     */
    public function formName()
    {
        return "SettingsGeneralForm";
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['timezone'], 'integer'],
            [['seo_description', 'seo_title'], 'trim'],
            [['name', 'seo_title'], 'string', 'max' => 255],
            [['seo_description'], 'string', 'max' => 2000],
            [['logo', 'favicon'], 'string', 'max' => 255],
        ];
    }

    /**
     * Update General settings
     * @param $postData
     * @return bool
     */
    public function updateSettings($postData)
    {
        error_log(print_r($postData,1),0);
        if (!$this->load($postData) || !$this->validate()) {
            return false;
        }

        // Processing files
        foreach (static::$_files as $formField => $validationRules) {
            $attribute = str_replace('File', '', $formField);

            // Delete deleted files
            $isDeleted  = $this->isAttributeChanged($attribute);
            if ($isDeleted) {
                $urlToDelete = $this->getOldAttribute($attribute);
                $this->_deleteFromCdn($urlToDelete);
                continue;
            }

            // Processing new files
            $fileInstance = UploadedFile::getInstance($this, $formField);

            if (!($fileInstance instanceof UploadedFile)) {
                continue;
            }

            $fileValidator = new FileValidator($validationRules);

            if (!$fileValidator->validate($fileInstance, $message)) {
                $this->addError($attribute, $message);
                return false;
            };

            $tmpFilePath = $fileInstance->tempName;
            $mime = $fileInstance->type;

            $url = $this->_uploadToCdn($tmpFilePath, $mime);

            if (!$url) {
                $this->addError($attribute, Yii::t('admin', 'settings.message_cdn_upload_error'));
                return false;
            }

            $this->setAttribute($attribute, $url);


            // Delete updated files
            $urlToDelete = $this->getOldAttribute($attribute);
            $this->_deleteFromCdn($urlToDelete);
        }

        return $this->save(false);
    }

    /**
     * Upload file to CDN
     * @param $pathToFile
     * @param $mime
     * @return bool|string
     */
    private function _uploadToCdn($pathToFile, $mime)
    {
        /** @var BaseCdn $cdn */
        $cdn = Cdn::getCdn();

        try {
            $id = $cdn->uploadFromPath($pathToFile, $mime);
            $url = $cdn->getUrl($id);
        } catch (\Exception $e) {
            return null;
        }

        return $url;
    }

    /**
     * Delete file from CDN
     * @param $cdnUrl
     * @return bool
     */
    private function _deleteFromCdn($cdnUrl)
    {
        /** @var BaseCdn $cdn */
        $cdn = Cdn::getCdn();

        try {
            $cdn->deleteByUrl($cdnUrl);
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }


}
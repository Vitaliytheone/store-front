<?php

namespace frontend\modules\admin\models\forms;

use common\models\store\Files;
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
        'logoFile' => [
            'type' => Files::FILE_TYPE_LOGO,
            'rules' => ['extensions' => 'png, jpg, gif',
                'maxSize' => 3.146e6,
                'mimeTypes' => [
                    BaseCdn::MIME_JPEG,
                    BaseCdn::MIME_PNG,
                    BaseCdn::MIME_GIF
                ]
            ]
        ],
        'faviconFile' => [
            'type' => Files::FILE_TYPE_FAVICON,
            'rules' => ['extensions' => 'png, jpg, gif, ico',
                'maxSize' => 0.512e6,
                'mimeTypes' => [
                    BaseCdn::MIME_JPEG,
                    BaseCdn::MIME_PNG,
                    BaseCdn::MIME_GIF,
                    BaseCdn::MIME_ICO
                ]
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
        return array_merge(parent::rules(), [
            [['seo_description', 'seo_title', 'admin_email'], 'trim'],
            ['admin_email', 'email'],
        ]);
    }

    /**
     * Update General settings
     * @param $postData
     * @return bool
     */
    public function updateSettings($postData)
    {
        if (!$this->load($postData) || !$this->save()) {
            return false;
        }

        // Processing files
        foreach (static::$_files as $formField => $fileData) {
            $attribute = str_replace('File', '', $formField);

            $fileInstance = UploadedFile::getInstance($this, $formField);

            if (!($fileInstance instanceof UploadedFile)) {
                continue;
            }

            $fileValidator = new FileValidator($fileData['rules']);

            if (!$fileValidator->validate($fileInstance, $message)) {
                $this->addError($attribute, $message);
                return false;
            };

            $tmpFilePath = $fileInstance->tempName;
            $mime = $fileInstance->type;

            $storeFile = Files::updateStoreSettingsFile($fileData['type'], $tmpFilePath, $mime);

            if (!$storeFile) {
                $this->addError($attribute, Yii::t('admin', 'settings.message_cdn_upload_error'));
                return false;
            }

        }

        return $this->save(false);
    }

}
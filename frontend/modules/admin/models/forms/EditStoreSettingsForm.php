<?php

namespace frontend\modules\admin\models\forms;

use common\models\store\ActivityLog;
use common\models\store\Files;
use common\models\stores\StoreAdminAuth;
use Yii;
use common\models\stores\Stores;
use yii\validators\FileValidator;
use yii\web\UploadedFile;
use common\components\cdn\BaseCdn;
use yii\web\User;


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
     * Current User
     * @var User
     */
    private $_user;

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
            ['timezone', 'filter', 'filter' => function($value) { return (int)$value; }]
        ]);
    }

    /**
     * Set current user
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->_user = $user;
    }

    /**
     * Return current user
     * @return User
     */
    public function getuser()
    {
       return $this->_user;
    }


    /**
     * Update General settings
     * @param $postData
     * @return bool
     */
    public function updateSettings($postData)
    {
        if (!$this->load($postData) || !$this->validate()) {
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

        $changedAttributes = $this->getDirtyAttributes();

        if (!$this->save(false)) {

            return false;
        }

        $this->_changeLog($changedAttributes);

        return true;
    }

    /**
     * Write changes to log
     * @param $changedAttributes
     * @return bool
     */
    private function _changeLog($changedAttributes)
    {
        /** @var StoreAdminAuth $identity */
        $identity = $this->getuser()->getIdentity(false);

        if (isset($changedAttributes['name'])) {
            ActivityLog::log($identity, ActivityLog::E_SETTINGS_GENERAL_STORE_NAME_CHANGED);
        }

        if (isset($changedAttributes['timezone'])) {
            ActivityLog::log($identity, ActivityLog::E_SETTINGS_GENERAL_STORE_TIMEZONE_CHANGED);
        }

        if (isset($changedAttributes['admin_email'])) {
            ActivityLog::log($identity, ActivityLog::E_SETTINGS_GENERAL_STORE_ADMIN_EMAIL_CHANGED);
        }

        if (isset($changedAttributes['seo_title'])) {
            ActivityLog::log($identity, ActivityLog::E_SETTINGS_GENERAL_STORE_SEO_TITLE_CHANGED);
        }

        if (isset($changedAttributes['seo_description'])) {
            ActivityLog::log($identity, ActivityLog::E_SETTINGS_GENERAL_STORE_SEO_META_DESCRIPTION_CHANGED);
        }
    }

}
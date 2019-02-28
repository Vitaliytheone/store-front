<?php

namespace sommerce\modules\admin\models\forms;


use common\models\sommerce\ActivityLog;
use common\models\sommerce\Files;
use common\models\sommerces\PaymentMethodsCurrency;
use common\models\sommerces\StoreAdminAuth;
use common\models\sommerces\StorePaymentMethods;
use sommerce\helpers\ConfigHelper;
use Yii;
use common\models\sommerces\Stores;
use yii\helpers\ArrayHelper;
use yii\validators\FileValidator;
use yii\web\UploadedFile;
use common\components\cdn\BaseCdn;
use yii\web\User;

/**
 * Class EditStoreSettingsForm
 * @property UploadedFile $faviconFile
 * @property UploadedFile $logoFile
 * @package sommerce\modules\admin\models\forms
 */
class EditStoreSettingsForm extends Stores
{
    public $faviconFile;
    public $logoFile;

    /**
     * Uploaded files rules
     * @var array
     */
    private static $_files;

    /**
     * File validator custom messages
     * @var array
     */
    private static $_file_validator_messages;

    /**
     * Current User
     * @var StoreAdminAuth
     */
    private $_user;

    /** @inheritdoc */
    public function init()
    {
        parent::init();

        $fileValidator = new FileValidator();
        $maxUploadFileSize = $fileValidator->getSizeLimit();

        $logoFileSizeLimit = $maxUploadFileSize < Yii::$app->params['logoFileSizeLimit'] ? $maxUploadFileSize : Yii::$app->params['logoFileSizeLimit'];
        $icoFileSizeLimit = $maxUploadFileSize < Yii::$app->params['iconFileSizeLimit'] ? $maxUploadFileSize : Yii::$app->params['iconFileSizeLimit'];

        static::$_files = [
            'logoFile' => [
                'type' => Files::FILE_TYPE_LOGO,
                'rules' => [
                    'extensions' => 'png, jpg, gif',
                    'maxSize' =>  $logoFileSizeLimit,
                    'mimeTypes' => [
                        BaseCdn::MIME_JPEG,
                        BaseCdn::MIME_PNG,
                        BaseCdn::MIME_GIF
                    ]
                ]
            ],
            'faviconFile' => [
                'type' => Files::FILE_TYPE_FAVICON,
                'rules' => [
                    'extensions' => 'png, jpg, gif, ico',
                    'maxSize' => $icoFileSizeLimit,
                    'mimeTypes' => [
                        BaseCdn::MIME_JPEG,
                        BaseCdn::MIME_PNG,
                        BaseCdn::MIME_GIF,
                        BaseCdn::MIME_ICO
                    ],
                ]
            ],
        ];

        /** Init custom file validator messages  */
        static::$_file_validator_messages = [
            'message' => Yii::t('admin', 'component.file_validator.message'),
            'uploadRequired' => Yii::t('admin', 'component.file_validator.uploadRequired'),
            'tooMany' => Yii::t('admin', 'component.file_validator.toMany'),
            'tooFew' => Yii::t('admin', 'component.file_validator.tooFew'),
            'tooBig' => Yii::t('admin', 'component.file_validator.tooBig'),
            'tooSmall' => Yii::t('admin', 'component.file_validator.tooSmall'),
            'wrongMimeType' => Yii::t('admin', 'component.file_validator.wrongMimeType'),
            'wrongExtension' => Yii::t('admin', 'component.file_validator.wrongExtension'),
        ];
    }

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
            [['seo_description', 'seo_title'], 'trim'],
            [['custom_header', 'custom_footer'], 'string', 'max' => 10000],
            ['timezone', 'filter', 'filter' => function($value) { return (int)$value; }],

            ['currency', 'required'],
            ['currency', 'in', 'range' => array_keys(ConfigHelper::getCurrenciesList())],
        ]);
    }

    /**
     * Set current user
     * @param StoreAdminAuth $user
     */
    public function setUser(StoreAdminAuth $user)
    {
        $this->_user = $user;
    }

    /**
     * Return current user
     * @return StoreAdminAuth
     */
    public function getuser()
    {
       return $this->_user;
    }

    /**
     * Return uploaded files params
     * @return array
     */
    public function getUploadedFilesLimits()
    {
        return [
            'logoFileSizeLimit' => Yii::$app->formatter->asShortSize(ArrayHelper::getValue(static::$_files, 'logoFile.rules.maxSize')),
            'iconFileSizeLimit' => Yii::$app->formatter->asShortSize(ArrayHelper::getValue(static::$_files, 'faviconFile.rules.maxSize')),
        ];
    }

    /**
     * Update General settings
     * @param $postData
     * @return bool
     * @throws \Throwable
     * @throws \yii\base\Exception
     */
    public function updateSettings($postData): bool
    {
        $currentCurrency = $this->currency;

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

            $fileValidator = new FileValidator(array_merge($fileData['rules'], static::$_file_validator_messages));

            if (!$fileValidator->validate($fileInstance, $message)) {
                $this->addError($attribute, $message);
                return false;
            }

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

        if ($currentCurrency != $this->currency) {
            $storeMethods = StorePaymentMethods::find()->where(['store_id' => $this->id])->orderBy('position')->all();
            $currencyMethods = PaymentMethodsCurrency::find()
                ->where(['currency' => $this->currency])
                ->asArray()
                ->indexBy('method_id')
                ->all();

            $first = 1; // first position number

            foreach ($storeMethods as $method) {
                if (!array_key_exists($method->method_id, $currencyMethods)) {
                    $method->delete();
                } else {
                    $method->position = $first++;
                    $method->currency_id = $currencyMethods[$method->method_id]['id'];
                    $method->save(false);
                }
            }
        }

        $this->_changeLog($changedAttributes);

        return true;
    }

    /**
     * Write changes to log
     * @param $changedAttributes
     * @throws \Throwable
     */
    private function _changeLog($changedAttributes)
    {
        /** @var StoreAdminAuth $identity */
        $identity = $this->getuser();

        if (isset($changedAttributes['name'])) {
            ActivityLog::log($identity, ActivityLog::E_SETTINGS_GENERAL_STORE_NAME_CHANGED);
        }

        if (isset($changedAttributes['timezone'])) {
            ActivityLog::log($identity, ActivityLog::E_SETTINGS_GENERAL_STORE_TIMEZONE_CHANGED);
        }

        if (isset($changedAttributes['seo_title'])) {
            ActivityLog::log($identity, ActivityLog::E_SETTINGS_GENERAL_STORE_SEO_TITLE_CHANGED);
        }

        if (isset($changedAttributes['seo_description'])) {
            ActivityLog::log($identity, ActivityLog::E_SETTINGS_GENERAL_STORE_SEO_META_DESCRIPTION_CHANGED);
        }

        if (isset($changedAttributes['currency'])) {
            ActivityLog::log($identity, ActivityLog::E_SETTINGS_GENERAL_STORE_CURRENCY_CHANGED);
        }
    }

    /**
     * Check if currency changes
     * @param $currency string
     * @return bool
     */
    public function currencyChange($currency): bool
    {
        $currentCurrency = $this->currency;

        if ($currentCurrency != $currency) {
            $storeMethods = StorePaymentMethods::find()
                ->where(['store_id' => $this->id])
                ->indexBy('method_id')
                ->all();
            $currencies = PaymentMethodsCurrency::find()
                ->where(['in', 'method_id', array_keys($storeMethods)])
                ->andWhere(['currency' => $currency])
                ->all();
            if (count($storeMethods) !== count($currencies)) {
                return true;
            }
        }

        return false;
    }

}

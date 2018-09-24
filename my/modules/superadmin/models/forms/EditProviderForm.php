<?php

namespace my\modules\superadmin\models\forms;

use common\models\panels\AdditionalServices;
use yii\base\Model;
use Yii;

/**
 * Class EditProviderForm
 * @package my\modules\superadmin\models\forms
 */
class EditProviderForm extends Model
{
    public $provider_id;
    public $name;
    public $apihelp;
    public $status;
    public $type;
    public $name_script;
    public $start_count;
    public $refill;
    public $cancel;
    public $sender_params;
    public $send_method;
    public $service_view;
    public $service_options;
    public $provider_service_id_label;
    public $provider_service_settings;
    public $provider_service_api_error;
    public $service_description;
    public $service_auto_min;
    public $service_auto_max;
    public $provider_rate;
    public $service_auto_rate;
    public $import;
    public $getstatus_params;

    /**
     * @var AdditionalServices
     */
    private $_provider;

    public function __construct($data = null, array $config = [])
    {
        parent::__construct($config);

        foreach ((array)$data as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['provider_id', 'name'], 'required'],
            [['provider_id', 'type', 'status', 'start_count', 'refill', 'cancel', 'provider_service_id_label'], 'integer'],
            [['name'], 'string', 'max' => 32],
            [['apihelp'], 'string', 'max' => 2000],
        ];
    }

    /**
     * Set provider
     * @param AdditionalServices $provider
     */
    public function setProvider(AdditionalServices $provider)
    {
        $this->_provider = $provider;
    }

    /**
     * Save provider settings
     * @return bool
     */
    public function save(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        foreach ($this->attributes as $key => $value) {
            $this->_provider->$key = $value;
        }

        if (!$this->_provider->save()) {
            $this->addErrors($this->_provider->getErrors());
            return false;
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'provider_id' => Yii::t('app/superadmin', 'providers.modal_edit_provider.provider_id'),
            'name' => Yii::t('app/superadmin', 'providers.modal_edit_provider.name'),
            'apihelp' => Yii::t('app/superadmin', 'providers.modal_edit_provider.apihelp'),
            'status' => Yii::t('app/superadmin', 'providers.modal_edit_provider.status'),
            'type' => Yii::t('app/superadmin', 'providers.modal_edit_provider.type'),
            'name_script' => Yii::t('app/superadmin', 'providers.modal_edit_provider.name_script'),
            'start_count' => Yii::t('app/superadmin', 'providers.modal_edit_provider.start_count'),
            'refill' => Yii::t('app/superadmin', 'providers.modal_edit_provider.refill'),
            'cancel' => Yii::t('app/superadmin', 'providers.modal_edit_provider.cancel'),
            'sender_params' => Yii::t('app/superadmin', 'providers.modal_edit_provider.sender_params'),
            'send_method' => Yii::t('app/superadmin', 'providers.modal_edit_provider.send_method'),
            'service_view' => Yii::t('app/superadmin', 'providers.modal_edit_provider.service_view'),
            'service_options' => Yii::t('app/superadmin', 'providers.modal_edit_provider.service_options'),
            'provider_service_id_label' => Yii::t('app/superadmin', 'providers.modal_edit_provider.provider_service_id_label'),
            'provider_service_settings' => Yii::t('app/superadmin', 'providers.modal_edit_provider.provider_service_settings'),
            'provider_service_api_error' => Yii::t('app/superadmin', 'providers.modal_edit_provider.provider_service_api_error'),
            'service_description' => Yii::t('app/superadmin', 'providers.modal_edit_provider.service_description'),
            'service_auto_min' => Yii::t('app/superadmin', 'providers.modal_edit_provider.service_auto_min'),
            'service_auto_max' => Yii::t('app/superadmin', 'providers.modal_edit_provider.service_auto_max'),
            'provider_rate' => Yii::t('app/superadmin', 'providers.modal_edit_provider.provider_rate'),
            'service_auto_rate' => Yii::t('app/superadmin', 'providers.modal_edit_provider.service_auto_rate'),
            'import' => Yii::t('app/superadmin', 'providers.modal_edit_provider.import'),
            'getstatus_params' => Yii::t('app/superadmin', 'providers.modal_edit_provider.getstatus_params'),
        ];
    }
}
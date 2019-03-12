<?php
namespace sommerce\modules\admin\models\forms;

use common\models\panels\AdditionalServices;
use common\models\sommerce\ActivityLog;
use common\models\sommerces\StoreAdminAuth;
use common\models\sommerces\StoreProviders;
use common\models\sommerces\Stores;
use Yii;
use yii\base\Model;
use yii\web\User;

/**
 * Class CreateProviderForm
 * @package app\modules\superadmin\models\forms
 */
class CreateProviderForm extends Model {

    public $name;

    /**
     * @var AdditionalServices
     */
    protected $_provider;

    /**
     * @var Stores
     */
    protected $_store;

    /**
     * @var User
     */
    protected $_user;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'prepareFilter'],
            [['name'], 'checkProvider'],
            [['name'], 'string', 'max' => 100],
        ];
    }

    /**
     * Set store
     * @param Stores $store
     */
    public function setStore($store)
    {
        $this->_store = $store;
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
    public function getUser()
    {
        return $this->_user;
    }

    /**
     * Save domain
     * @return bool
     */
    public function save()
    {
        $attributes = $this->attributes;
        if (!$this->validate()) {
            $this->attributes = $attributes;
            return false;
        }

        $provider = new StoreProviders();
        $provider->provider_id = $this->_provider->provider_id;
        $provider->store_id = $this->_store->id;

        if (!$provider->save()) {
            $this->addErrors($provider->getErrors());
            $this->attributes = $attributes;
            return false;
        }

        /** @var StoreAdminAuth $identity */
        $identity = $this->getUser()->getIdentity(false);

        ActivityLog::log($identity, ActivityLog::E_SETTINGS_PROVIDERS_PROVIDER_ADEDD, $provider->id, $this->name);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t('admin', 'settings.providers_m_name'),
        ];
    }

    /**
     * Prepare provider fite value
     * @param $attribute
     * @return bool
     */
    public function prepareFilter($attribute)
    {
        if ($this->hasErrors($attribute)) {
            return false;
        }

        $value = mb_strtolower($this->{$attribute});
        $value = trim($value);
        $value = preg_replace("/^(http(s)?)\:\/\//uis", "", $value);
        $value = preg_replace("/^(www\.)/uis", "", $value);
        $value = parse_url('http://' . $value, PHP_URL_HOST);

        if (empty($value)) {
            $this->addError($attribute, 'Incorrect ' . $attribute . ' value.');
            return false;
        }

        $this->{$attribute} = $value;

        return true;
    }

    /**
     * Validate provider
     * @param $attribute
     * @return bool
     */
    public function checkProvider($attribute)
    {
        if ($this->hasErrors($attribute)) {
            return false;
        }

        $value = $this->{$attribute};

        if (($this->_provider = AdditionalServices::findOne([
            'name' => $value,
            'store' => 1,
            'status' => 0,

        ]))) {

            if (StoreProviders::findOne([
                'provider_id' => $this->_provider->provider_id,
                'store_id' => $this->_store->id
            ])) {
                $this->addError($attribute, Yii::t('admin', 'settings.errors_providers_exist'));
                return false;
            }
            return true;
        }
        $this->addError($attribute, Yii::t('admin', 'settings.errors_providers_vaild'));

        return false;
    }
}

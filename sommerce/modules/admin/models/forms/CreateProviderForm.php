<?php
namespace sommerce\modules\admin\models\forms;

use common\models\store\ActivityLog;
use common\models\stores\Providers;
use common\models\stores\StoreAdminAuth;
use common\models\stores\StoreProviders;
use common\models\stores\Stores;
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
     * @var Providers
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
        $provider->provider_id = $this->_provider->id;
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
            'name' => 'Provider name',
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

        if (($this->_provider = Providers::findOne([
            'site' => $value
        ]))) {

            if (StoreProviders::findOne([
                'provider_id' => $this->_provider->id,
                'store_id' => $this->_store->id
            ])) {
                $this->addError($attribute, 'Provider already exist.');
                return false;
            }
            return true;
        }

        $key = Yii::$app->params['getyourpanelKey'];

        $result = file_get_contents("http://getyourpanel.com/checkpanel?key={$key}&domain=" . urlencode($value));

        $result = json_decode($result, true);

        if (empty($result['result']) || 'ok' != $result['result']) {
            $this->addError($attribute, 'Incorrect ' . $attribute . ' value.');
            return false;
        }

        if (!$this->_provider) {
            $this->_provider = new Providers();
            $this->_provider->site = $this->name;
            $this->_provider->type = Providers::TYPE_INTERNAL;
            $this->_provider->protocol = !empty($result['ssl']) ? Providers::PROTOCOL_HTTPS : Providers::PROTOCOL_HTTP;

            if (!$this->_provider->save()) {
                $this->addError($attribute, 'Incorrect ' . $attribute . ' value.');
                return false;
            }
        }

        $this->{$attribute} = $value;

        return true;
    }
}
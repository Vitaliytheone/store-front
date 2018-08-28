<?php
namespace sommerce\modules\admin\models\forms;

use common\models\store\ActivityLog;
use common\models\stores\StoreAdminAuth;
use common\models\stores\StoreProviders;
use common\models\stores\Stores;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\web\User;

/**
 * Class CreateProviderForm
 * @package app\modules\superadmin\models\forms
 */
class ProvidersListForm extends Model {

    public $providers;

    public $id;
    public $api_key;

    /**
     * @var Stores
     */
    protected $_store;

    /** @var User */
    protected $_user;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['providers'], 'safe'],
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
        /** @var StoreAdminAuth $identity */
        $identity = $this->getUser()->getIdentity(false);

        foreach ((array)$this->providers as $provider) {
            $key = ArrayHelper::getValue($provider, 'key');
            $apiKey = ArrayHelper::getValue($provider, 'api_key');

            if (empty($key)) {
                continue;
            }

            $model = StoreProviders::findOne([
                'provider_id' => $key,
                'store_id' => $this->_store->id
            ]);

            if ($model) {

                $model->apikey = $apiKey;

                if ($model->isAttributeChanged('apikey')) {
                    ActivityLog::log($identity, ActivityLog::E_SETTINGS_PROVIDERS_PROVIDER_API_KEY_CHANGED, $model->id, $model->provider->name);
                }

                $model->save();
            }
        }

        return true;
    }
}
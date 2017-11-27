<?php
namespace frontend\modules\admin\models\forms;

use common\models\stores\StoreProviders;
use common\models\stores\Stores;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

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
     * Save domain
     * @return bool
     */
    public function save()
    {
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
                $model->save();
            }
        }
        return true;
    }
}
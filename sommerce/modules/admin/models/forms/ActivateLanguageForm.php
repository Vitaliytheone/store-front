<?php

namespace sommerce\modules\admin\models\forms;

use common\models\sommerces\Stores;
use sommerce\modules\admin\helpers\LanguagesHelper;
use yii\base\Exception;
use yii\base\Model;
use Yii;

/**
 * Class ActivateLanguageForm
 * @package sommerce\modules\admin\models\forms
 */
class ActivateLanguageForm extends Model
{
    /** @var Stores */
    private $_store;

    /**
     * Set current store
     * @param Stores $store
     */
    public function setStore(Stores $store)
    {
        $this->_store = $store;
    }

    /**
     * Return current store
     * @return Stores
     */
    public function getStore()
    {
        return $this->_store;
    }

    /**
     * Activate store language by language code
     * @param $code
     * @return bool
     */
    public function activateStoreLanguage($code)
    {
        $store = $this->getStore();

        if (empty($store) || !$store instanceof Stores) {
            return false;
        }

        $configLanguages = Yii::$app->params['languages'];

        if (!in_array($code, array_keys($configLanguages))) {
            return false;
        }

        $store->language = $code;

        return $store->save(false);
    }

    /**
     * Add new store language
     * @param $code string Language code
     * @return bool
     * @throws Exception
     */
    public function addStoreLanguage($code)
    {
        return LanguagesHelper::createStoreLanguage($this->getStore(), $code);
    }
}
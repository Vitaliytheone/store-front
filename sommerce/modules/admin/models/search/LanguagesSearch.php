<?php

namespace sommerce\modules\admin\models\search;

use common\models\store\Languages;
use common\models\stores\StoreDefaultMessages;
use common\models\stores\Stores;
use sommerce\modules\admin\helpers\LanguagesHelper;
use yii\base\Model;
use yii\db\Query;
use Yii;
use yii\helpers\ArrayHelper;

class LanguagesSearch extends Model
{
    /** @var Stores   */
    private $_store;

    /**
     * Cached store langs list
     * @var array|null
     */
    static $storeLangs;

    /** @var   */
    static $configLangsList;

    /** @inheritdoc */
    public function init()
    {
        parent::init();

        static::$configLangsList = LanguagesHelper::getConfigLanguagesList(true);
    }

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
     * Return store languages codes list
     * @return array|null
     */
    public function getStoreLangs()
    {
        if (empty(static::$storeLangs)) {
            static::$storeLangs = Languages::find()
                ->select(['code'])
                ->column();
        }

        return static::$storeLangs;
    }

    /**
     * Return merged lists of store languages
     * and languages which has translations in store_default_messages
     * @return array
     */
    public function storeLanguages()
    {
        $langsList = array_flip(array_merge($this->getStoreLangs(), LanguagesHelper::getDefaultLanguages()));

        array_walk($langsList, function (&$langData, $langCode){
            $langData = [
                'name' => ArrayHelper::getValue(static::$configLangsList, $langCode),
                'active' => $langCode === $this->getStore()->language,
            ];
        });

        return $langsList;
    }

    /**
     * Return available languages list for store
     * which can be added by store admin
     * @return array
     */
    public function availableLanguages()
    {
        $storeLangs = $this->storeLanguages();

        $langList = array_diff_key(static::$configLangsList,  $storeLangs);

        return $langList;
    }
}
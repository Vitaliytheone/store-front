<?php

namespace sommerce\modules\admin\models\search;

use common\models\panels\AdditionalServices;
use common\models\stores\StoreProviders;
use common\models\stores\Stores;
use yii\db\Query;

/**
 * Class ProvidersSearch
 * @package app\models\search
 */
class ProvidersSearch extends BaseSearch
{
    protected $_store;

    /**
     * @param Stores $store
     */
    public function setStore(Stores $store)
    {
        $this->_store = $store;
    }
    /**
     * Build sql query
     * @return Query
     */
    public function buildQuery()
    {
        
        $storeProvidersTable = StoreProviders::tableName();
        $providersTable = AdditionalServices::tableName();

        $query = (new Query())
            ->select([
                'p.provider_id as id',
                'p.name as site',
                'sp.apikey',
            ])
            ->from("$storeProvidersTable sp")
            ->leftJoin("$providersTable p", 'p.provider_id = sp.provider_id')
            ->andWhere([
                'sp.store_id' => $this->_store->id
            ]);

        return $query;
    }

    /**
     * Search domains
     * @return array
     */
    public function search()
    {
        $query = clone $this->buildQuery();

        $items = $query
            ->orderBy(['site' => SORT_ASC])
            ->all();


        return [
            'models' => $items,
        ];
    }
}
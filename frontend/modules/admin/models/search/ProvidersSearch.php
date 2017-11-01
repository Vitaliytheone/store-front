<?php

namespace frontend\modules\admin\models\search;

use common\models\stores\Stores;
use Yii;
use yii\db\Query;

/**
 * Class OrdersSearch
 * @package app\models\search
 */
class ProvidersSearch extends BaseSearch
{
    /**
     * Build sql query
     * @return Query
     */
    public function buildQuery()
    {
        /**
         * @var $store Stores
         */
        $store = Yii::$app->store->getInstance();

        $query = (new Query())
            ->select([
                'p.id',
                'p.site',
                'sp.apikey',
            ])
            ->from('store_providers sp')
            ->leftJoin('providers p', 'p.id = sp.provider_id')
            ->andWhere([
                'sp.store_id' => $store->id
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
            ->orderBy([
                'id' => SORT_DESC
            ])
            ->all();


        return [
            'models' => $items,
        ];
    }
}
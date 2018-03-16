<?php

namespace my\modules\superadmin\models\search;

use common\models\panels\PaymentGateway;
use yii\db\ActiveQuery;

/**
 * Class PaymentGatewaySearch
 * @package my\modules\superadmin\models
 */
class PaymentGatewaySearch extends PaymentGateway
{
    private $params;

    public $rows;

    /**
     * Set search parameters
     * @param array $params
     */
    public function setParams($params)
    {
        $this->params = $params;
    }

    /**
     * Build main search query
     * @return ActiveQuery the newly created [[ActiveQuery]] instance.
     */
    private function buildQuery()
    {
        $query = static::find()
        ->andWhere([
            'pid' => '-1'
        ]);

        return $query;
    }

    /**
     * Search payment gateway
     * @return array
     */
    public function search()
    {
        $query = clone $this->buildQuery();

        $models = $query->orderBy([
                'position' => SORT_ASC
            ])
            ->all();

        return $models;
    }
}
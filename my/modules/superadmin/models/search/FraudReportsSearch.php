<?php

namespace my\modules\superadmin\models\search;

use yii\base\Model;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * Class FraudReportsSearch
 * @package my\modules\superadmin\models\search
 */
class FraudReportsSearch extends Model
{

    use SearchTrait;

    /**
     * Get query params
     * @return array
     */
    public function getParams(): array
    {
        return [
            'status' => ArrayHelper::getValue($this->params, 'status', 'all'),
        ];
    }

    /**
     * @param null|integer $status
     * @return Query
     */
    private function buildQuery($status = null): Query
    {
        $query = (new Query())
            ->select('*')
            ->from('paypal_fraud_reports');

        if ($status !== 'all' || $status !== null) {
            $query->andWhere([
                'status' => $status
            ]);
        }

        return $query;
    }

    public function search()
    {
        $status = ArrayHelper::getValue($this->params, 'status', 'all');

        $reports = $this->buildQuery($status);
        $reports->orderBy(['id' => SORT_DESC]);

        return $reports->all();
    }

    /**
     * @return array
     */
    public function navs(): array
    {
        // NEED TO CHANGE TEST DATA
        return [
            'all' => 'All',
            0 => 'Pending',
            1 => 'Accepted',
            2 => 'Rejected',
        ];
    }
}
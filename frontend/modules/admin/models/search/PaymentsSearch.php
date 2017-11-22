<?php

namespace frontend\modules\admin\models\search;

use Yii;
use yii\base\Model;
use yii\db\Query;
use yii\validators\EmailValidator;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;

/**
 * Class PaymentsSearch
 * @package frontend\modules\admin\models\search
 */
class PaymentsSearch extends Model
{
    public $status;
    public $method;
    public $query;

    private $_db;
    private $_queryActiveFilters;

    const PAGE_SIZE = 100;

    public function init()
    {
        $this->_db = yii::$app->store->getInstance()->db_name;
        parent::init();
    }

    /**
     * Apply query filters to specified query object
     * @param $queryObject \yii\db\Query()
     * @param array $queryFilters
     * @param array $excludedFilterGroups Array names of the excluded filter groups. Example: ['status', 'method'] will be excluded
     */
    private function _applyFilters(&$queryObject, $queryFilters = [], $excludedFilterGroups = [])
    {
        if (!$queryFilters) {
            return;
        }

        foreach ($queryFilters as $filterGroupName => $filterGroup) {
            foreach ($filterGroup as $filterName => $filter) {
                // Excluded filters
                if (in_array($filterGroupName, $excludedFilterGroups)) continue;
                // Were filters
                if ($filterName === 'where') {
                    $queryObject->andFilterWhere($filter);
                }
                // Having filters
                if ($filterName === 'having') {
                    $queryObject->andFilterHaving($filter);
                }
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        // Search & filters rules
        return [
            [['status',], 'integer'],
            [['method',], 'string'],
            ['query', 'safe'],
        ];
    }

    /**
     * Search in Payments collection
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params = [])
    {
        $query = (new Query())
            ->select([
                'id', 'customer', 'amount', 'method', 'fee', 'memo', 'status', 'updated_at',
            ])
            ->from("$this->_db.payments")
            ->indexBy('id')
            ->orderBy([
                'id' => SORT_DESC,
            ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => static::PAGE_SIZE,
            ],
        ]);

        $this->setAttributes($params);
        if (!$this->validate()) {
            return $dataProvider;
        }

        // Query filters
        if(isset($this->status)) {
            $filter = ['status' => $this->status];
            $this->_queryActiveFilters['status']['where'] = $filter;
        }

        if (isset($this->method)) {
            $filter = ['method' => $this->method];
            $this->_queryActiveFilters['method']['where'] = $filter;
        }

        $this->_applyFilters($query, $this->_queryActiveFilters);

        $searchQuery = trim($this->query);
        if ($searchQuery === '') {
            return $dataProvider;
        }

        // Searches:
        // 1. Strong by `customer` if $searchQuery : valid Email
        // 2. Soft by `memo`
        $searchFilter = null;
        $emailValidator = new EmailValidator();

        if ($emailValidator->validate($searchQuery)) {
            $query->andFilterWhere(['customer' => $searchQuery]);
        } else {
            $query->andFilterWhere(['like', 'memo', $searchQuery]);
        }

        return $dataProvider;
    }

    /**
     * Counts payments by `status`
     * @return array
     */
    public function countsByStatus()
    {
        $counts = (new Query())
            ->select(['status', 'COUNT(*) count'])
            ->from("$this->_db.payments")
            ->groupBy(['status'])
            ->indexBy('status')
            ->all();

        return $counts;
    }

    /**
     * Counts payments by `methods`
     * @return array
     */
    public function countsByMethods()
    {
        $storeId = yii::$app->store->getId();

        $methodsList = (new Query())
            ->select(['method'])
            ->from('payment_methods')
            ->where(['store_id' => $storeId])
            ->indexBy('method')
            ->all();

        $countsByMethodsQuery = (new Query())
            ->select(['method', 'COUNT(*) count'])
            ->from("$this->_db.payments")
            ->groupBy('method')
            ->indexBy('method');

        $this->_applyFilters($countsByMethodsQuery, $this->_queryActiveFilters, ['method']);

        $counts = $countsByMethodsQuery->all();

        array_walk($methodsList, function(&$methodItem, $method) use ($counts) {
            $methodItem['count'] = ArrayHelper::getValue($counts, "$method.count", 0);
        });

        return $methodsList;
    }
}

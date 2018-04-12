<?php

namespace sommerce\modules\admin\models\search;

use Yii;
use yii\base\Model;
use yii\db\Query;
use yii\validators\EmailValidator;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use yii\base\Exception;
use sommerce\modules\admin\components\Url;
use sommerce\helpers\UiHelper;
use common\models\store\Payments;
use common\models\stores\PaymentMethods;


/**
 * Class PaymentsSearch
 * @property integer $status
 * @property integer $method
 * @property integer $query
 * @property ActiveDataProvider $_dataProvider
 * @package sommerce\modules\admin\models\search
 */
class PaymentsSearch extends Model
{
    public $status;
    public $method;
    public $query;

    private $_db;
    private $_paymentsTable;
    private $_paymentMethodsTable;

    private $_queryActiveFilters;
    private $_dataProvider;

    const PAGE_SIZE = 100;

    public function init()
    {
        $this->_db = yii::$app->store->getInstance()->db_name;
        $this->_paymentsTable = $this->_db . "." . Payments::tableName();
        $this->_paymentMethodsTable = PaymentMethods::tableName();

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
            ->from($this->_paymentsTable)
            ->indexBy('id')
            ->orderBy([
                'id' => SORT_DESC,
            ]);

        $this->_dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => static::PAGE_SIZE,
            ],
        ]);

        $this->setAttributes($params);
        if (!$this->validate()) {
            return $this->_dataProvider;
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

        // Search
        $searchQuery = trim($this->query);

        if ($searchQuery !== '') {

            // For payment ids lists
            // Example query string: 23, 432, 33, 42
            $queryList = array_unique(explode(',', str_replace(' ', '', $searchQuery)));
            $paymentIds = [];
            foreach ($queryList as $item) {
                if (ctype_digit($item)) {
                    $paymentIds[] = $item;
                }
            }

            // Searches:
            // 1. Strong by `customer` if $searchQuery : valid Email
            // 2. Strong by payment id
            // 3. Soft by `memo`
            $searchFilter = null;
            $emailValidator = new EmailValidator();

            $searchFilter = null;

            if ($emailValidator->validate($searchQuery)) {
                $searchFilter = ['customer' => $searchQuery];
            } elseif ($paymentIds) {
                $searchFilter = ['in', 'id', $paymentIds];
            }

            $searchFilter = [
                'or',
                ['like', 'memo', $searchQuery],
                $searchFilter,
            ];

            $this->_queryActiveFilters['search']['where'] = $searchFilter;
        }

        $this->_applyFilters($query, $this->_queryActiveFilters);

        return $this->_dataProvider;
    }

    /**
     * Counts payments by `status`
     * @return array
     */
    public function countsByStatus()
    {
        $countsQuery = (new Query())
            ->select(['status', 'COUNT(*) count'])
            ->from($this->_paymentsTable)
            ->groupBy(['status'])
            ->indexBy('status');

        $this->_applyFilters($countsQuery, $this->_queryActiveFilters, ['status']);

        $counts = $countsQuery->all();

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
            ->from($this->_paymentMethodsTable)
            ->where(['store_id' => $storeId])
            ->indexBy('method')
            ->all();

        $countsByMethodsQuery = (new Query())
            ->select(['method', 'COUNT(*) count'])
            ->from($this->_paymentsTable)
            ->groupBy('method')
            ->indexBy('method');

        $this->_applyFilters($countsByMethodsQuery, $this->_queryActiveFilters, ['method']);

        $counts = $countsByMethodsQuery->all();

        array_walk($methodsList, function(&$methodItem, $method) use ($counts) {
            $methodItem['count'] = ArrayHelper::getValue($counts, "$method.count", 0);
        });

        return $methodsList;
    }

    /**
     * Return status filter menu buttons data
     * @param array $options
     * @return array
     */
    public function getStatusFilterButtons($options = [])
    {
        $buttons = [
            'all' => [
                'title' => Yii::t('admin', 'payments.payments_all'),
                'filter' => null,
                'url' => null,
                'count' => null,
            ],
            Payments::STATUS_AWAITING => [
                'title' => Payments::getStatusName(Payments::STATUS_AWAITING),
            ],
            Payments::STATUS_COMPLETED => [
                'title' => Payments::getStatusName(Payments::STATUS_COMPLETED),
            ],
            Payments::STATUS_FAILED => [
                'title' => Payments::getStatusName(Payments::STATUS_FAILED),
            ],
            Payments::STATUS_REFUNDED => [
                'title' => Payments::getStatusName(Payments::STATUS_REFUNDED),
            ],
        ];

        $countsPaymentsByStatus = $this->countsByStatus();

        array_walk($buttons, function (&$button, $filter) use ($countsPaymentsByStatus, $options) {
            if ($filter === 'all') {
                $count = array_sum(array_column($countsPaymentsByStatus, 'count'));
                $url = Url::toRoute('/payments');
            } else {
                $count = ArrayHelper::getValue($countsPaymentsByStatus, "$filter.count" );
                $url = Url::current(['status' => $filter]);
            }

            if ($options) {
                $buttonOptions = ArrayHelper::getValue($options, $filter, null);
                if ($buttonOptions) {
                    $button['options'] = $buttonOptions;
                }
            }

            $button['id'] = "status_button_$filter";
            $button['filter'] = $filter;
            $button['url'] = $url;
            $button['active'] = UiHelper::isFilterActive('status', $filter);
            $button['count'] = $count;
        });

        return $buttons;
    }

    /**
     * Return methods filter menu items
     * @return array
     */
    public function getMethodFilterItems()
    {
        $methodsFilterMenuItems = $this->countsByMethods();

        /* Populate methods filter by additional data */
        array_walk($methodsFilterMenuItems, function(&$menuItem){
            $method = $menuItem['method'];
            $menuItem['url'] = Url::current(['method' => $method]);
            $menuItem['active'] = UiHelper::isFilterActive('method', $method);
            $menuItem['method_title'] = PaymentMethods::getMethodName($method);
        });

        $allMethodsMenuItem = [
            'method' => 'all',
            'method_title' => Yii::t('admin', "payments.payment_method_all"),
            'active' =>  UiHelper::isFilterActive('method', 'all'),
            'count' => array_sum(array_column($methodsFilterMenuItems,'count')),
            'url' => Url::current(['method' => null]),
        ];

        array_unshift($methodsFilterMenuItems, $allMethodsMenuItem);

        return $methodsFilterMenuItems;
    }

    /**
     * Return found suborders formatted for view
     * @return array
     * @throws Exception
     */
    public function getPayments()
    {
        if (!$this->_dataProvider) {
            throw new Exception('First do a search!');
        }

        $payments = $this->_dataProvider->getModels();

        /* Populate payments list by additional formats and data */
        array_walk($payments, function(&$payment) {
            $payment['method_title'] = PaymentMethods::getMethodName($payment['method']);
            $payment['status_title'] = Payments::getStatusName($payment['status']);
            $payment['updated_at_formatted'] = Yii::$app->formatter->asDatetime($payment['updated_at'],'yyyy-MM-dd HH:mm:ss');
        });

        return $payments;
    }

}

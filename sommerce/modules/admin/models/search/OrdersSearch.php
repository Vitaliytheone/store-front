<?php

namespace sommerce\modules\admin\models\search;

use common\models\sommerce\Orders;
use common\models\sommerce\Packages;
use common\models\sommerce\Products;
use common\models\sommerces\Stores;
use Yii;
use yii\base\Model;
use yii\base\Exception;
use yii\db\Query;
use yii\validators\EmailValidator;
use yii\helpers\ArrayHelper;
use sommerce\modules\admin\components\Url;
use sommerce\helpers\UiHelper;
use common\models\sommerce\Suborders;
use yii\data\ActiveDataProvider;

/**
 * Orders Search model
 * @property integer $status
 * @property integer $mode
 * @property integer $product
 * @property integer $query
 * @property ActiveDataProvider $_dataProvider
 * @property array $_queryActiveFilters Uses for current query filters storing. Format: [$filterName => [$filter => [....]]]
 */
class OrdersSearch extends Model
{
    public $status;
    public $mode;
    public $product;
    public $query;
    
    // Tables shortcuts
    private $_db;
    private $_ordersTable;
    private $_subordersTable;
    private $_packagesTable;
    private $_productsTable;

    private $_queryActiveFilters;
    private $_dataProvider;

    const PAGE_SIZE = 100;

    public static $statusFilters = [
        Suborders::STATUS_AWAITING,
        Suborders::STATUS_PENDING,
        Suborders::STATUS_IN_PROGRESS,
        Suborders::STATUS_COMPLETED,
        Suborders::STATUS_CANCELED,
        Suborders::STATUS_FAILED,
        Suborders::STATUS_ERROR,
    ];

    /* Suborder accepted statuses for changes from admin panel */
    public static $acceptedStatuses = [
        Suborders::STATUS_PENDING,
        Suborders::STATUS_IN_PROGRESS,
        Suborders::STATUS_COMPLETED,
    ];

    /* Suborder statuses when `Change status` action is disallowed */
    public static $disallowedChangeStatusStatuses = [
        Suborders::STATUS_AWAITING,
        Suborders::STATUS_CANCELED,
        Suborders::STATUS_COMPLETED,
    ];

    /* Suborder statuses when `Cancel suborder` action is disallowed */
    public static $disallowedCancelStatuses = [
        Suborders::STATUS_AWAITING,
        Suborders::STATUS_CANCELED,
        Suborders::STATUS_COMPLETED,
    ];

    /* Suborder statuses when `View details` action disallowed */
    public static $disallowedDetailsStatuses = [
        Suborders::STATUS_AWAITING,
        Suborders::STATUS_CANCELED,
    ];

    /**
     * @param Stores $store
     */
    public function setStore(Stores $store)
    {
        $this->_db = $store->db_name;
        $this->_ordersTable = $this->_db . "." . Orders::tableName();
        $this->_subordersTable = $this->_db . "." . Suborders::tableName();
        $this->_packagesTable = $this->_db . "." . Packages::tableName();
        $this->_productsTable = $this->_db . "." . Products::tableName();
    }


    /**
     * Apply query filters to specified query object
     * @param $queryObject \yii\db\Query()
     * @param array $queryFilters
     * @param array $excludedFilterGroups Array names of the excluded filter groups. Example: ['status', 'mode'] will be excluded
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
            [['status', 'mode', 'product'], 'integer'],
            ['query', 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'status' => 'Status',
            'mode' => 'Mode',
            'product' => 'Product',
        ];
    }

    /**
     * Search in Orders collection
     * @param array $params Filters params
     * @return ActiveDataProvider
     */
    public function search($params = [])
    {
        $query = (new Query())
            ->select([
                'o.id', 'checkout_id', 'customer', 'created_at',
            ])
            ->from("$this->_ordersTable o")
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

        $this->attributes = $params;
        if (!$this->validate()) {
            return $this->_dataProvider;
        }

        // Query filters
        if(isset($this->status)) {
            $statusOrderIdsSubquery = (new Query())
                ->select('order_id')
                ->from($this->_subordersTable)
                ->where(['status' => $this->status ])
                ->groupBy('order_id');
            $filter = ['o.id' => $statusOrderIdsSubquery];
            $this->_queryActiveFilters['status']['where'] = $filter;
        }

        if (isset($this->mode)) {
            $modeOrderIdsSubquery = (new Query())
                ->select("order_id")
                ->from($this->_subordersTable)
                ->where(['mode' => $this->mode ])
                ->groupBy('order_id');
            $filter = ['o.id' => $modeOrderIdsSubquery];
            $this->_queryActiveFilters['mode']['where'] = $filter;
        }

        if (isset($this->product)) {
            $productOrderIdsSubquery = (new Query())
                ->select("so.order_id")
                ->from("$this->_subordersTable so")
                ->leftJoin("$this->_packagesTable pk", 'pk.id = so.package_id')
                ->leftJoin("$this->_productsTable pr",'pr.id = pk.product_id')
                ->where(['pk.product_id' => $this->product])
                ->groupBy('so.order_id');
            $filter = ['o.id' => $productOrderIdsSubquery];
            $this->_queryActiveFilters['product']['where'] = $filter;
        }

        // Search
        $searchQuery = trim($this->query);
        if ($searchQuery !== '') {

            // For order_id and provider_order_id lists
            // Query string example: 1, 22ED-3DD-32, 2, 3, 4, 9909
            $queryList = array_unique(explode(',', str_replace(' ', '', $searchQuery)));
            $orderIds = [];
            $providerOrderIds = [];

            foreach ($queryList as $item) {
                if (ctype_digit($item)) {
                    $orderIds[] = $item;
                }
                $providerOrderIds[] = $item;
            }

            // Searches:
            // 1. Search strong by `id` &  soft by `link` if $searchQuery : number
            // 2. Search strong by `customer` if $searchQuery : valid Email
            // 3. Search strong by orderIds list or providerOrderIds list
            // 3.1. Search soft by `link` if $searchQuery : some string
            $emailValidator = new EmailValidator();
            $searchFilter = null;
            if (ctype_digit($searchQuery)) {
                $searchOrderIdsSubquery = (new Query())
                    ->select('order_id')
                    ->from($this->_subordersTable)
                    ->where([
                        'or',
                        ['order_id' => $searchQuery],
                        ['like', 'link', $searchQuery]
                    ])
                    ->groupBy('order_id');
                $searchFilter = ['o.id' => $searchOrderIdsSubquery];
            } elseif ($emailValidator->validate($searchQuery)) {
                $searchOrderIdsSubquery = (new Query())
                    ->select('id order_id')
                    ->from($this->_ordersTable)
                    ->where(['customer' => $searchQuery])
                    ->groupBy('order_id');
                $searchFilter = ['o.id' => $searchOrderIdsSubquery];
            } else {
                $searchOrderIdsSubquery = (new Query())
                    ->select('order_id')
                    ->from($this->_subordersTable)
                    ->where([
                        'or',
                        ['in', 'order_id', $orderIds],
                        ['in', 'provider_order_id', $providerOrderIds],
                        ['like', 'link', $searchQuery]
                    ])
                    ->groupBy('order_id');
                $searchFilter = ['o.id' => $searchOrderIdsSubquery];
            }

            $this->_queryActiveFilters['search']['where'] = $searchFilter;
        }

        $this->_applyFilters($query, $this->_queryActiveFilters);

        return $this->_dataProvider;
    }

    /**
     * Return suborders counts for each status
     * Statuses are: self::$statusFilters
     * @return array
     */
    public function geSubordersCountsByStatus()
    {
        $query = (new Query())
            ->select (['status', 'COUNT(mode) count'])
            ->from ("$this->_subordersTable so")
            ->leftJoin("$this->_ordersTable o", 'o.id = so.order_id')
            ->groupBy('status')
            ->indexBy('status');

        $this->_applyFilters($query, $this->_queryActiveFilters, ['status']);

        $presentSubordersCounts = $query->all();

        $subordersCounts = [];

        foreach (static::$statusFilters as $filter) {

            $currentStatusCount = ArrayHelper::getValue($presentSubordersCounts, "$filter.count", 0);

            $subordersCounts[$filter] = [
                'status' => $filter,
                'count' => $currentStatusCount,
            ];
        }

        return $subordersCounts;
    }

    /**
     * Return Status Filter buttons data
     * @param array $options
     * @return array
     */
    public function getStatusFilterButtons($options = [])
    {
        $subordersByStatusCounts = $this->geSubordersCountsByStatus();

        $buttons = [
            'all' => [
                'title' => Yii::t('admin', 'orders.filter_status_all'),
                'url' => Url::toRoute('/orders'),
                'count' => array_sum(array_column($subordersByStatusCounts, 'count')),
            ],
            Suborders::STATUS_AWAITING => [
                'title' => Suborders::getStatusName(Suborders::STATUS_AWAITING),
            ],
            Suborders::STATUS_PENDING => [
                'title' => Suborders::getStatusName(Suborders::STATUS_PENDING),
            ],
            Suborders::STATUS_IN_PROGRESS => [
                'title' => Suborders::getStatusName(Suborders::STATUS_IN_PROGRESS),
            ],
            Suborders::STATUS_COMPLETED => [
                'title' => Suborders::getStatusName(Suborders::STATUS_COMPLETED),
            ],
            Suborders::STATUS_CANCELED => [
                'title' => Suborders::getStatusName(Suborders::STATUS_CANCELED),
            ],
            Suborders::STATUS_FAILED => [
                'title' => Suborders::getStatusName(Suborders::STATUS_FAILED),
            ],
            Suborders::STATUS_ERROR => [
                'title' => Suborders::getStatusName(Suborders::STATUS_ERROR),
            ],
        ];

        array_walk($buttons, function (&$button, $filter) use ($subordersByStatusCounts, $options) {

            if ($filter !== 'all') {
                $button['count'] = ArrayHelper::getValue($subordersByStatusCounts, "$filter.count" );
                $button['url'] =  Url::current(['status' => $filter]);
            }

            if ($options) {
                $buttonOptions = ArrayHelper::getValue($options, $filter, null);
                if ($buttonOptions) {
                    $button['options'] = $buttonOptions;
                }
            }

            $button['id'] = "status_button_$filter";
            $button['filter'] = $filter;
            $button['active'] = UiHelper::isFilterActive('status', $filter);
        });

        return $buttons;
    }

    /**
     * Return Products filter ui items data
     * @return array
     */
    public function productFilterItems()
    {
        // Get all products
        $productsList = (new Query())
            ->select(['id','name'])
            ->from($this->_productsTable)
            ->indexBy('id')
            ->all();

        // Get count suborders for product
        $subordersByProductsQuery = (new Query())
            ->select(['pr.id, COUNT(pr.id) count'])
            ->from(['pr' => $this->_productsTable])
            ->rightJoin(['pk' => $this->_packagesTable], 'pk.product_id = pr.id')
            ->rightJoin(['so' => $this->_subordersTable], 'pk.id = so.package_id')
            ->rightJoin(['o' => $this->_ordersTable], 'o.id = so.order_id')
            ->groupBy('pr.id')
            ->indexBy('id');

        $this->_applyFilters($subordersByProductsQuery, $this->_queryActiveFilters, ['product']);

        $subordersByProductsCounts = $subordersByProductsQuery->all();

        $filterItems = [
            'all' => [
                'product' => 'all',
                'url' => Url::current(['product' => null]),
                'name' => Yii::t('admin', 'orders.filter_product_all'),
                'count' => array_sum(ArrayHelper::getColumn($subordersByProductsCounts, 'count')),
                'active' => UiHelper::isFilterActive('product', 'all')
            ],
        ];

        foreach ($productsList as $productId => $product) {
            $packagesCount = ArrayHelper::getValue($subordersByProductsCounts, "$productId.count", 0);
            $filterItems[] = [
                'product' => $productId,
                'url' => Url::current(['product' => $productId]),
                'name' => $product['name'],
                'count' => $packagesCount,
                'active' => UiHelper::isFilterActive('product', $productId),
            ];
        }

        return $filterItems;
    }

    /**
     * Return Mode filter ui items data
     * @return array
     */
    public function modeFilterItems()
    {
        $query = (new Query())
            ->select (['mode', 'COUNT(mode) count'])
            ->from ("$this->_subordersTable so")
            ->leftJoin("$this->_ordersTable o", 'o.id = so.order_id')
            ->groupBy('mode')
            ->indexBy('mode');

        $this->_applyFilters($query, $this->_queryActiveFilters, ['mode']);
        $modeFilterStat = $query->all();

        $filterItems = [
            'all' => [
                'title' => Yii::t('admin', 'orders.filter_status_all'),
            ],
            Suborders::MODE_MANUAL => [
                'title' => Suborders::getModeName(Suborders::MODE_MANUAL),
            ],
            Suborders::MODE_AUTO => [
                'title' => Suborders::getModeName(Suborders::MODE_AUTO),
            ],
        ];

        array_walk($filterItems, function(&$item, $filter) use($modeFilterStat) {
            if ($filter === 'all') {
                $count = array_sum(array_column($modeFilterStat, 'count')) ;
                $url = Url::current(['mode' => null]);
            } else {
                $count = ArrayHelper::getValue($modeFilterStat, "$filter.count", 0);
                $url = Url::current(['mode' => $filter]);
            }

            $item['mode'] = $filter;
            $item['url'] = $url;
            $item['count'] = $count;
            $item['active'] = UiHelper::isFilterActive('mode', $filter);
        });

        return $filterItems;
    }

    /**
     * Return found Orders with Suborders array formatted for view
     * @return array
     * @throws Exception
     */
    public function getOrders()
    {
        if (!$this->_dataProvider) {
            throw new Exception('First do a search!');
        }

        $orders = $this->_dataProvider->getModels();
        $orderIds = array_keys($orders);

        $suborders = (new Query())
            ->select([
                'so.id suborder_id', 'so.order_id', 'so.package_id', 'pk.product_id',
                'so.amount', 'so.link', 'so.quantity', 'so.status', 'so.mode',
                'pr.name product_name',
            ])
            ->from("$this->_subordersTable so")
            ->leftJoin("$this->_packagesTable pk",'so.package_id = pk.id')
            ->leftJoin("$this->_productsTable pr",'pk.product_id = pr.id')
            ->where(['so.order_id' => $orderIds])
            ->indexBy('suborder_id')
            ->all();

        $formatter = Yii::$app->formatter;

        // Populate each order by additional data
        array_walk($orders, function(&$order, $orderId) use ($suborders, $formatter){

            // Get order suborders
            $suborders =  array_filter($suborders, function($suborder) use ($orderId){
                return $suborder['order_id'] == $orderId;
            },ARRAY_FILTER_USE_BOTH);

            // Populate each suborder by additional data
            array_walk($suborders, function(&$suborder) use ($suborders) {
                $suborder['status_title'] = Suborders::getStatusName($suborder['status']);
                $suborder['mode_title'] = Suborders::getModeName($suborder['mode']);
                $suborder['action_menu'] = $this->getActionMenu($suborder['mode'], $suborder['status']);
            });

            $order['created_at'] = $formatter->asDatetime($order['created_at'], 'yyyy-MM-dd HH:mm:ss');
            $order['suborders'] = $suborders;
        });

        return $orders;
    }

    /**
     * Return action menu or null
     * @param $mode
     * @param $status
     * @return array|null
     */
    public function getActionMenu($mode, $status)
    {
        // Create `change status` menu
        $changeStatus = false;

        $status = (int)$status;
        $mode = (int)$mode;

        if (!in_array($status, static::$disallowedChangeStatusStatuses)) {
            foreach (static::$acceptedStatuses as $acceptedStatus) {
                if ($status == $acceptedStatus) {
                    continue;
                }
                $changeStatus[] = [
                    'status' => $acceptedStatus,
                    'status_title' => Suborders::getStatusName($acceptedStatus),
                ];
            }
        }

        // `details` menu show
        $details = ($mode === Suborders::MODE_AUTO) && !in_array($status, static::$disallowedDetailsStatuses);

        // `resend` menu show
        $resend = $status === Suborders::STATUS_FAILED;

        // `cancel` menu show
        $cancel = !in_array($status, static::$disallowedCancelStatuses);

        $actionMenu = ($details || $resend || $changeStatus || $cancel) ? [
            'details' => $details,
            'resend' => $resend,
            'status' => $changeStatus,
            'cancel' => $cancel,
        ] : null;

        return $actionMenu;
    }

}

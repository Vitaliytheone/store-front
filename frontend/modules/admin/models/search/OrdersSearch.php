<?php

namespace frontend\modules\admin\models\search;

use Yii;
use yii\base\Exception;
use yii\db\Query;
use yii\validators\EmailValidator;
use yii\helpers\ArrayHelper;
use frontend\modules\admin\components\Url;
use frontend\helpers\UiHelper;
use common\models\store\Suborders;
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
class OrdersSearch extends \yii\base\Model
{
    public $status;
    public $mode;
    public $product;
    public $query;

    private $_db;
    private $_queryActiveFilters;

    private $_dataProvider;

    const PAGE_SIZE = 100;

    /* Suborder accepted statuses for changes from admin panel */
    public static $acceptedStatuses = [
        Suborders::STATUS_PENDING,
        Suborders::STATUS_IN_PROGRESS,
        Suborders::STATUS_COMPLETED,
    ];

    /* Suborder statuses when `Cancel suborder` action is disallowed */
    public static $disallowedCancelStatuses = [
        Suborders::STATUS_AWAITING,
        Suborders::STATUS_CANCELED,
        Suborders::STATUS_COMPLETED,
    ];

    /* Suborder statuses when `Change status` action is disallowed */
    public static $disallowedChangeStatusStatuses = [
        Suborders::STATUS_AWAITING,
        Suborders::STATUS_CANCELED,
        Suborders::STATUS_COMPLETED,
    ];

    /* Suborder statuses when `View details` action disallowed */
    public static $disallowedDetailsStatuses = [
        Suborders::STATUS_AWAITING,
        Suborders::STATUS_CANCELED,
    ];

    public static $statusFilters = [
        Suborders::STATUS_AWAITING,
        Suborders::STATUS_PENDING,
        Suborders::STATUS_IN_PROGRESS,
        Suborders::STATUS_COMPLETED,
        Suborders::STATUS_CANCELED,
        Suborders::STATUS_FAILED,
        Suborders::STATUS_ERROR,
    ];

    public static $modeFilters = [
        Suborders::MODE_MANUAL => [
            'caption' => 'Manual',
        ],
        Suborders::MODE_AUTO => [
            'caption' => 'Auto',
        ],
    ];

    public function init()
    {
        $this->_db = yii::$app->store->getInstance()->db_name;
        parent::init();
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
            ->from("$this->_db.orders o")
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
                ->from("$this->_db.suborders")
                ->where(['status' => $this->status ])
                ->groupBy('order_id');
            $filter = ['o.id' => $statusOrderIdsSubquery];
            $this->_queryActiveFilters['status']['where'] = $filter;
        }

        if (isset($this->mode)) {
            $modeOrderIdsSubquery = (new Query())
                ->select("order_id")
                ->from("$this->_db.suborders")
                ->where(['mode' => $this->mode ])
                ->groupBy('order_id');
            $filter = ['o.id' => $modeOrderIdsSubquery];
            $this->_queryActiveFilters['mode']['where'] = $filter;
        }

        if (isset($this->product)) {
            $productOrderIdsSubquery = (new Query())
                ->select("so.order_id")
                ->from("$this->_db.suborders so")
                ->leftJoin("$this->_db.packages pk", 'pk.id = so.package_id')
                ->leftJoin("$this->_db.products pr",'pr.id = pk.product_id')
                ->where(['pk.product_id' => $this->product])
                ->groupBy('so.order_id');
            $filter = ['o.id' => $productOrderIdsSubquery];
            $this->_queryActiveFilters['product']['where'] = $filter;
        }

        $this->_applyFilters($query, $this->_queryActiveFilters);


        $searchQuery = trim($this->query);
        if ($searchQuery === '') {
            return $this->_dataProvider;
        }

        // Searches:
        // 1. Search strong by `id` &  soft by `link` if $searchQuery : number
        // 2. Search strong by `customer` if $searchQuery : valid Email
        // 3. Search soft by `link` if $searchQuery : some string
        $emailValidator = new EmailValidator();
        $searchFilter = null;
        if (ctype_digit($searchQuery)) {
            $searchOrderIdsSubquery = (new Query())
                ->select('order_id')
                ->from("$this->_db.suborders")
                ->where([  'or', ['order_id' => $searchQuery], ['like', 'link', $searchQuery]])
                ->groupBy('order_id');
            $searchFilter = ['o.id' => $searchOrderIdsSubquery];
        } elseif ($emailValidator->validate($searchQuery)) {
            $searchOrderIdsSubquery = (new Query())
                ->select('id order_id')
                ->from("$this->_db.orders")
                ->where(['customer' => $searchQuery])
                ->groupBy('order_id');
            $searchFilter = ['o.id' => $searchOrderIdsSubquery];
        } else {
            $searchOrderIdsSubquery = (new Query())
                ->select('order_id')
                ->from("$this->_db.suborders")
                ->where(['like', 'link', $searchQuery])
                ->groupBy('order_id');
            $searchFilter = ['o.id' => $searchOrderIdsSubquery];
        }

        // Apply query filter
        if ($searchFilter) {
            $query->andFilterWhere($searchFilter);
        }

        return $this->_dataProvider;
    }

    /**
     * Return array of allowed statuses object for Order admin actions
     * @return array
     */
    public static function allowedActionStatuses()
    {
        return array_filter(static::$statusFilters, function($filterKey) {
            return in_array($filterKey, static::$acceptedStatuses);
        }, ARRAY_FILTER_USE_KEY );
    }

    /**
     * Return suborders counts for each status
     * Statuses are: self::$statusFilters
     * @return array
     */
    public function geSubordersCountsByStatus()
    {
        $presentSubordersCounts = (new Query())
            ->select(['status', 'COUNT(*) count'])
            ->from("$this->_db.suborders")
            ->groupBy('status')
            ->indexBy('status')
            ->all();

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
                'filter' => null,
                'url' => null,
                'count' => null,
            ],
            Suborders::STATUS_AWAITING => [
                'title' => Suborders::getStatusTitle(Suborders::STATUS_AWAITING),
            ],
            Suborders::STATUS_PENDING => [
                'title' => Suborders::getStatusTitle(Suborders::STATUS_PENDING),
            ],
            Suborders::STATUS_IN_PROGRESS => [
                'title' => Suborders::getStatusTitle(Suborders::STATUS_IN_PROGRESS),
            ],
            Suborders::STATUS_COMPLETED => [
                'title' => Suborders::getStatusTitle(Suborders::STATUS_COMPLETED),
            ],
            Suborders::STATUS_CANCELED => [
                'title' => Suborders::getStatusTitle(Suborders::STATUS_CANCELED),
            ],
            Suborders::STATUS_FAILED => [
                'title' => Suborders::getStatusTitle(Suborders::STATUS_FAILED),
            ],
            Suborders::STATUS_ERROR => [
                'title' => Suborders::getStatusTitle(Suborders::STATUS_ERROR),
            ],
        ];

        array_walk($buttons, function (&$button, $filter) use ($subordersByStatusCounts, $options) {
            if ($filter === 'all') {
                $count = array_sum(array_column($subordersByStatusCounts, 'count'));
                $url = Url::toRoute('/orders');
            } else {
                $count = ArrayHelper::getValue($subordersByStatusCounts, "$filter.count" );
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
     * Return Products filter ui items data
     * @return array
     */
    public function productFilterItems()
    {
        // Get all products
        $productsList = (new Query())
            ->select(['id','name'])
            ->from("$this->_db.products")
            ->indexBy('id')
            ->all();

        // Get count suborders for product
        $subordersByProductsQuery = (new Query())
            ->select(['pr.id, COUNT(pr.id) count'])
            ->from("$this->_db.suborders so")
            ->leftJoin("$this->_db.packages pk", 'pk.id = so.package_id')
            ->leftJoin("$this->_db.products pr", 'pk.product_id = pr.id')
            ->leftJoin("$this->_db.orders o", 'o.id = so.order_id')
            ->groupBy('pr.id')
            ->orderBy([
                'pr.id' => SORT_ASC,
            ])
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
            ->from ("$this->_db.suborders so")
            ->leftJoin("$this->_db.orders o", 'o.id = so.order_id')
            ->groupBy('mode' );

        $this->_applyFilters($query, $this->_queryActiveFilters, ['mode']);
        $modeFilterStat = $query->createCommand()->queryAll();

        $filterItems = [
            'all' => [
                'title' => Yii::t('admin', 'orders.filter_status_all'),
            ],
            Suborders::MODE_MANUAL => [
                'title' => Suborders::getModeTitle(Suborders::MODE_MANUAL),
            ],
            Suborders::MODE_AUTO => [
                'title' => Suborders::getModeTitle(Suborders::MODE_AUTO),
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
     * Return found Orders with Suborders array
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
            ->from("$this->_db.suborders so")
            ->leftJoin("$this->_db.packages pk",'so.package_id = pk.id')
            ->leftJoin("$this->_db.products pr",'pk.product_id = pr.id')
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
                $span =  $this->_getRowSpan($suborder, $suborders);
                $actionMenu = $this->_getActionMenu($suborder);
                if ($span) {
                    $suborder['row_span'] = $span;
                }

                if ($actionMenu) {
                    $suborder['action'] = $actionMenu;
                }

                $suborder['status_title'] = Suborders::getStatusTitle($suborder['status']);
                $suborder['mode_title'] = Suborders::getModeTitle($suborder['mode']);
            });

            $order['created_at'] = $formatter->asDatetime($order['created_at'], 'yyyy-MM-dd HH:mm:ss');
            $order['suborders'] = $suborders;
        });

        return $orders;
    }

    /**
     * Return row span count for suborders row or null
     * @param $suborder
     * @param $suborders
     * @return int|null
     */
    private function _getRowSpan($suborder, $suborders)
    {
        $subordersCount = count($suborders);
        $firstSuborder = array_values($suborders)[0];
        $isFirst = (ArrayHelper::getValue($suborder, 'suborder_id') === ArrayHelper::getValue($firstSuborder, 'suborder_id'));
        return (($subordersCount > 1) && $isFirst) ? $subordersCount : null;
    }

    /**
     * Return action menu or null
     * @param $suborder
     * @return array|null
     */
    private function _getActionMenu($suborder)
    {
        $details = [];
        $resend =  [];
        $changeStatus = [];
        $cancel = [];

        $actionMenu = ($details || $resend || $changeStatus || $cancel) ? [
            'details' => $details,
            'resend' => $resend,
            'changeStatus' => $changeStatus,
            'cancel' => $cancel,
        ] : null;

        return $actionMenu;
    }

}

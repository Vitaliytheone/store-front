<?php

namespace frontend\modules\admin\models;

use Yii;
use yii\db\Query;
use yii\validators\EmailValidator;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use frontend\modules\admin\data\OrdersActiveDataProvider;

/**
 * Orders Search model
 * @property integer $status
 * @property integer $mode
 * @property integer $product
 * @property integer $search
 * @property array $_queryActiveFilters Uses for current query filters storing. Format: [$filterName => [$filter => [....]]]
 */
class OrderSearch extends \yii\base\Model
{
    public $status;
    public $mode;
    public $product;
    public $query;

    private $_queryActiveFilters;

    const PAGE_SIZE = 100;

    const FILTER_STATUS_AWAITING    = 1;
    const FILTER_STATUS_PENDING     = 2;
    const FILTER_STATUS_IN_PROGRESS = 3;
    const FILTER_STATUS_COMPLETED   = 4;
    const FILTER_STATUS_CANCELED    = 5;
    const FILTER_STATUS_FAILED      = 6;
    const FILTER_STATUS_ERROR       = 7;

    const FILTER_MODE_MANUAL        = 0;
    const FILTER_MODE_AUTO          = 1;

    public static $statusFilters = [
        self::FILTER_STATUS_AWAITING => [
            'caption' => 'Awaiting',
            'stat' => true,
            'stat-class' => 'm-badge m-badge--metal m-badge--wide',
        ],
        self::FILTER_STATUS_PENDING => [
            'caption' => 'Pending',
        ],
        self::FILTER_STATUS_IN_PROGRESS => [
            'caption' => 'In progress',
        ],
        self::FILTER_STATUS_COMPLETED => [
            'caption' => 'Completed',
        ],
        self::FILTER_STATUS_CANCELED => [
            'caption' => 'Canceled',
        ],
        self::FILTER_STATUS_FAILED => [
            'caption' => 'Failed',
            'stat' => true,
            'stat-class' => 'm-badge m-badge--danger',
        ],
        self::FILTER_STATUS_ERROR => [
            'caption' => 'Error',
            'stat' => true,
            'stat-class' => 'm-badge m-badge--danger',
        ],
    ];

    public static $modeFilters = [
        self::FILTER_MODE_MANUAL => [
            'caption' => 'Manual',
        ],
        self::FILTER_MODE_AUTO => [
            'caption' => 'Auto',
        ],
    ];

    // Allowed statuses in action admin menu at Orders page
    public static $actionAllowedStatuses = [
        self::FILTER_STATUS_PENDING,
        self::FILTER_STATUS_IN_PROGRESS,
        self::FILTER_STATUS_COMPLETED,
    ];

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
     * Statistic for Package filter
     * @param array $qParams
     * @param bool $total
     * @return array
     */
    public function packagesFilterStat($qParams = [], $total = true)
    {
        // Get all packages
        $packagesList = (new \yii\db\Query())
            ->select(['id','name'])
            ->from('packages')
            ->indexBy('id')
            ->all();

        // Get count orders for packages
        $ordersByPackagesQuery = (new \yii\db\Query())
            ->select(['package_id, COUNT(package_id) cnt'])
            ->from('orders o')
            ->orderBy([
                'package_id' => SORT_ASC,
            ])
            ->groupBy('package_id')
            ->indexBy('package_id');

        $this->_applyFilters($ordersByPackagesQuery, $this->_queryActiveFilters, ['package']);
        $ordersByPackages = $ordersByPackagesQuery->all();

        $filterStat = [];
        foreach ($packagesList as $packageId => $package) {
            $ordersPackage = ArrayHelper::getValue($ordersByPackages, $packageId, 0);
            $packageCount = $ordersPackage ? $ordersPackage['cnt'] : 0;
            $filterStat[] = [
                'package' => $packageId,
                'name' => $package['name'],
                'cnt' => $packageCount,
            ];
        }
        if ($total) {
            $sum = array_sum(ArrayHelper::getColumn($filterStat, 'cnt'));
            array_unshift($filterStat, [
                'package' => -1,
                'name' => 'All',
                'cnt' => $sum,
            ]);
        }
        return $filterStat;
    }

    /**
     * Statistic for Mode filter
     * @param array $qParam
     * @param bool $total
     * @return array
     */
    public function modeFilterStat($qParam = [], $total = true)
    {
        $query = (new \yii\db\Query())
            ->select (['mode', 'COUNT(mode) cnt'])
            ->from ('orders o')
            ->groupBy('mode' );

        $this->_applyFilters($query, $this->_queryActiveFilters, ['mode']);
        $modeFilterStat = $query->createCommand()->queryAll();

        //Populate filters array by filter name (caption) values
        $modeFilters = static::$modeFilters;
        array_walk($modeFilterStat, function(&$filter, $key) use ($modeFilters) {
            $filter['name'] = $modeFilters[$filter['mode']]['caption'];
        });

        if ($total) {
            $sum = array_sum(ArrayHelper::getColumn($modeFilterStat, 'cnt'));
            array_unshift($modeFilterStat, [
                'mode' => -1,
                'name' => 'All',
                'cnt' => $sum,
            ]);
        }
        return $modeFilterStat;
    }

    /**
     * Search in Orders collection
     * @param array $params Filters params
     * @return \frontend\modules\admin\data\OrdersActiveDataProvider
     */
    public function search($params = [])
    {
        $query = (new \yii\db\Query())
            ->select([
                'o.id order_id', 'so.id suborder_id', 'so.package_id', 'pk.product_id',
                'o.customer', 'o.created_at',
                'so.amount', 'so.link', 'so.quantity', 'so.status', 'so.mode',
                'pr.name product_name'
            ])
            ->from('orders o')
            ->leftJoin('suborders so','so.order_id = o.id')
            ->leftJoin('packages pk','pk.id = so.package_id')
            ->leftJoin('products pr','pk.product_id = pr.id')
            ->orderBy([
                'order_id' => SORT_DESC,
            ]);
        $dataProvider = new OrdersActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => static::PAGE_SIZE,
            ],
        ]);

        $this->attributes = $params;
        if (!$this->validate()) {
            return $dataProvider;
        }

        // Query filters
        if(isset($this->status)) {
            $statusOrderIdsSubquery = (new \yii\db\Query())
                ->select('order_id id')
                ->from('suborders')
                ->where(['status' => $this->status ])
                ->groupBy('order_id');
            $filter = ['o.id' => $statusOrderIdsSubquery];
            $this->_queryActiveFilters['status']['where'] = $filter;
        }
        if (isset($this->mode)) {
            $modeOrderIdsSubquery = (new \yii\db\Query())
                ->select('order_id id')
                ->from('suborders')
                ->where(['mode' => $this->mode ])
                ->groupBy('order_id');
            $filter = ['o.id' => $modeOrderIdsSubquery];
            $this->_queryActiveFilters['mode']['where'] = $filter;
        }
        if (isset($this->product)) {
            $productOrderIdsSubquery = (new \yii\db\Query())
                ->select('order_id id')
                ->from('suborders')
                ->where(['product_id' => $this->product ])
                ->groupBy('order_id');
            $filter = ['o.id' => $productOrderIdsSubquery];
            $this->_queryActiveFilters['mode']['where'] = $filter;
        }

        $this->_applyFilters($query, $this->_queryActiveFilters);

        $searchQuery = trim($this->query);
        if (!isset($searchQuery)) {
            return $dataProvider;
        }

        // Searches:
        // 1. Search strong by `id` &  soft by `link` if $searchQuery : number
        // 2. Search strong by `customer` if $searchQuery : valid Email
        // 3. Search soft by `link` if $searchQuery : some string
        $emailValidator = new EmailValidator();
        $searchFilter = null;
        if (ctype_digit($searchQuery)) {
            $searchOrderIdsSubquery = (new Query())
                ->select('order_id id')
                ->from('suborders')
                ->where([  'or', ['id' => $searchQuery], ['like', 'link', $searchQuery]])
                ->groupBy('order_id');
            $searchFilter = ['o.id' => $searchOrderIdsSubquery];
        } elseif ($emailValidator->validate($searchQuery)) {
            $searchOrderIdsSubquery = (new Query())
                ->select('order_id id')
                ->from('suborders')
                ->where(['customer' => $searchQuery])
                ->groupBy('order_id');
            $searchFilter = ['o.id' => $searchOrderIdsSubquery];
        } else {
            $searchOrderIdsSubquery = (new Query())
                ->select('order_id id')
                ->from('suborders')
                ->where(['like', 'link', $searchQuery])
                ->groupBy('order_id');
            $searchFilter = ['o.id' => $searchOrderIdsSubquery];
        }
        // Apply query filter
        if ($searchFilter) {
            $query->andFilterWhere($searchFilter);
        }

        return $dataProvider;
    }

    /**
     * Return array of allowed statuses for Order admin actions
     * @return array
     */
    public static function allowedActionStatuses()
    {
        return array_filter(self::$statusFilters, function($filterKey) {
            return in_array($filterKey, self::$actionAllowedStatuses);
        }, ARRAY_FILTER_USE_KEY );
    }


    /**
     * Return Orders count by status filter
     * @param $statusFilter
     * @return integer $ordersCount
     */
    public static function getOrdersCountByStatus($statusFilter)
    {
        $ordersCount = Yii::$app->db
            ->createCommand('
                SELECT COUNT(*) FROM
                  (SELECT order_id
                  FROM suborders
                  WHERE status = :filter GROUP BY order_id) counter
            ')
            ->bindValue(':filter', $statusFilter)
            ->queryScalar();
        return $ordersCount;
    }

    /**
     * Return Suborders count by status filter
     * @param $statusFilter
     * @return false|null|string
     */
    public static function getSubordersCountByStatus($statusFilter)
    {
        $subordersCount = Yii::$app->db
            ->createCommand('
              SELECT COUNT(*)
              FROM suborders so
              WHERE status = :filter;
            ')
            ->bindValue(':filter', $statusFilter)
            ->queryScalar();
        return $subordersCount;
    }

    /**
     * Return Status Filter buttons data
     * @return array
     */
    public function getStatusFilterButtons()
    {
        $buttons = [];
        // Show all button
        $buttons[] = [
            'id' => 'all_orders',
            'filter' => '',
            'caption' => 'All orders',
            'url' => Url::to('/admin/orders'),
            'stat' => false,
        ];
        foreach (self::$statusFilters as $filter => $filterData){
            $buttonId = implode('_', explode(' ', strtolower($filterData['caption'])));
            $isStat = ArrayHelper::getValue($filterData,'stat',false);
            $button = [
                'id' => $buttonId,
                'filter' => $filter,
                'caption' => $filterData['caption'],
                'url' => Url::current(['status' => $filter]),
            ];
            // Filter statistic
            if ($isStat) {
                $button['stat'] = [
                    'stat-class' => $filterData['stat-class'],
                    'count' => self::getSubordersCountByStatus($filter),
                ];
            } else {
                $button['stat'] = $isStat;
            }
            $buttons[] = $button;
        }
        return $buttons;
    }
}

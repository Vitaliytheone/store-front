<?php
namespace my\modules\superadmin\models\search;

use common\models\panels\InvoiceDetails;
use common\models\panels\Orders;
use yii\data\Pagination;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * Class OrdersSearch
 * @package my\modules\superadmin\models\search
 */
class OrdersSearch extends Orders {

    protected $pageSize = 100;

    public $rows;

    use SearchTrait;

    /**
     * Get parameters
     * @return array
     */
    public function getParams()
    {
        return [
            'query' => $this->getQuery(),
            'status' => isset($this->params['status']) ? $this->params['status'] : null,
            'item' => isset($this->params['item']) ? $this->params['item'] : null
        ];
    }

    /**
     * Build sql query
     * @param int $status
     * @param int $item
     * @param array $filters
     * @return Query
     */
    public function buildQuery($status = null, $item = null, $filters = [])
    {
        $searchQuery = $this->getQuery();

        $orders = static::find();

        if (null === $status || '' === $status) {
            if (empty($searchQuery)) {
                $orders->andWhere([
                    'orders.status' => [
                        Orders::STATUS_PAID,
                        Orders::STATUS_ADDED,
                        Orders::STATUS_PENDING,
                        Orders::STATUS_ERROR,
                        Orders::STATUS_CANCELED,
                    ]
                ]);
            }
        } else {
            $orders->andWhere([
                'orders.status' => $status
            ]);
        }

        if ($item) {
            $orders->andWhere([
                'orders.item' => $item
            ]);
        }

        $orders->joinWith(['customer', 'invoice']);

        if (!empty($searchQuery)) {
            $orders->andFilterWhere([
                'or',
                ['=', 'orders.id', $searchQuery],
                ['like', 'orders.domain', $searchQuery],
                ['like', 'customers.email', $searchQuery],
            ]);
        }

        $orders->orderBy([
            'orders.id' => SORT_DESC
        ]);

        return $orders;
    }

    /**
     * Search orders
     * @return array
     */
    public function search()
    {
        $status = ArrayHelper::getValue($this->params, 'status', null);
        $item = ArrayHelper::getValue($this->params, 'item', null);

        $query = clone $this->buildQuery($status, $item);

        $pages = new Pagination(['totalCount' => $this->count($status, $item)]);
        $pages->setPageSize($this->pageSize);
        $pages->defaultPageSize = $this->pageSize;

        if (!empty($this->params['pageSize'])) {
            $pages->setPageSize($this->params['pageSize']);
        }

        $orders = $query
            ->offset($pages->offset)
            ->limit($pages->limit)
            ->groupBy('orders.id');

        return [
            'models' => static::queryAllCache($orders),
            'pages' => $pages,
        ];
    }

    /**
     * Get count panels by type
     * @param int $status
     * @param int $item
     * @param array $filters
     * @return int|array
     */
    public function count($status = null, $item = null, $filters = [])
    {
        $query = clone $this->buildQuery($status, $item, $filters);

        if (!empty($filters['group']['status'])) {
            $query->select([
                'orders.status as status',
                'COUNT(DISTINCT orders.id) as rows'
            ])->groupBy('orders.status');

            return ArrayHelper::map(static::queryAllCache($query), 'status', 'rows');
        }

        if (!empty($filters['group']['item'])) {
            $query->select([
                'orders.item as item',
                'COUNT(DISTINCT orders.id) as rows'
            ])->groupBy('orders.item');

            return ArrayHelper::map(static::queryAllCache($query), 'item', 'rows');
        }

        $query->select('COUNT(DISTINCT orders.id)');

        return (int)static::queryScalarCache($query);
    }

    /**
     * Get navs
     * @return array
     */
    public function navs()
    {
        $statusCounters = $this->count(null, null, [
            'group' => [
                'status' => 1
            ],
        ]);

        return [
            null => 'All (' . $this->count() . ')',
            Orders::STATUS_ADDED => 'Completed (' . ArrayHelper::getValue($statusCounters, Orders::STATUS_ADDED, 0) . ')',
            Orders::STATUS_PAID => 'Ready (' . ArrayHelper::getValue($statusCounters, Orders::STATUS_PAID, 0) . ')',
            Orders::STATUS_PENDING => 'Pending (' . ArrayHelper::getValue($statusCounters, Orders::STATUS_PENDING, 0) . ')',
            Orders::STATUS_ERROR => 'Error (' . ArrayHelper::getValue($statusCounters, Orders::STATUS_ERROR, 0) . ')',
            Orders::STATUS_CANCELED => 'Canceled (' . ArrayHelper::getValue($statusCounters, Orders::STATUS_CANCELED, 0) . ')',
        ];
    }

    /**
     * Get aggregated items
     * @return array
     */
    public function getAggregatedItems()
    {
        $status = ArrayHelper::getValue($this->params, 'status', null);

        $itemCounters = $this->count($status, null, [
            'group' => [
                'item' => 1
            ],
        ]);

        $items = [
            0 => 'All (' . $this->count($status) . ')',
            Orders::ITEM_BUY_PANEL => 'Panels (' . ArrayHelper::getValue($itemCounters, Orders::ITEM_BUY_PANEL, 0) . ')',
            Orders::ITEM_BUY_CHILD_PANEL => 'Child Panels (' . ArrayHelper::getValue($itemCounters, Orders::ITEM_BUY_CHILD_PANEL, 0) . ')',
            Orders::ITEM_BUY_DOMAIN => 'Domains (' . ArrayHelper::getValue($itemCounters, Orders::ITEM_BUY_DOMAIN, 0) . ')',
            Orders::ITEM_BUY_SSL => 'Certificates (' . ArrayHelper::getValue($itemCounters, Orders::ITEM_BUY_SSL, 0) . ')',
            Orders::ITEM_BUY_STORE => 'Stores (' . ArrayHelper::getValue($itemCounters, Orders::ITEM_BUY_STORE, 0) . ')',
        ];

        return $items;
    }
}
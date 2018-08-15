<?php
namespace my\modules\superadmin\models\search;

use common\models\panels\Orders;
use my\helpers\DomainsHelper;
use yii\data\Pagination;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use Yii;

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

        $orders = new Query();
        $orders->from('orders');
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

        $orders->leftJoin('customers', 'customers.id = orders.cid');

        $orders->select([
            'orders.*',
            'invoices.id as invoice_id',
            'invoices.status as invoice_status',
            'customers.email as customer_email',
            'customers.id customer_id',
        ]);

        if (!empty($searchQuery)) {
            $orders->andFilterWhere([
                'or',
                ['=', 'orders.id', $searchQuery],
                ['like', 'orders.domain', $searchQuery],
                ['like', 'customers.email', $searchQuery],
            ]);
        }

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
            ->leftJoin('invoices', 'invoices.cid = orders.cid AND invoices.date = orders.date')
            ->offset($pages->offset)
            ->limit($pages->limit)
            ->orderBy([
                'orders.id' => SORT_DESC
            ])
            ->groupBy('orders.id');

        $models = static::queryAllCache($orders);

        return [
            'models' => $this->prepareData($models),
            'pages' => $pages,
        ];
    }

    /**
     * @param $data array
     * @return array
     */
    private function prepareData($data)
    {
        $resultData = array();

        foreach ($data as $key => $value) {

            $resultData[$key]['id'] = $value['id'];
            $resultData[$key]['cid'] = $value['cid'];
            $resultData[$key]['status'] = Orders::getStatuses()[$value['status']];
            $resultData[$key]['check_status'] = $value['status'];
            $resultData[$key]['hide'] = $value['hide'];
            $resultData[$key]['processing'] = $value['processing'];
            $resultData[$key]['date'] = date('Y-m-d', $value['date']);
            $resultData[$key]['time'] = date('H:i:s', $value['date']);
            $resultData[$key]['ip'] = $value['ip'];
            $resultData[$key]['domain'] = $value['domain'] ? DomainsHelper::idnToUtf8($value['domain']) : '';
            $resultData[$key]['details'] = $value['details'];
            $resultData[$key]['item'] = Orders::getItems()[$value['item']];
            $resultData[$key]['item_id'] = $value['item_id'];
            $resultData[$key]['invoice_id'] = $value['invoice_id'];
            $resultData[$key]['invoice_status'] = $value['invoice_status'];
            $resultData[$key]['customer_email'] = $value['customer_email'];
            $resultData[$key]['customer_id'] = $value['customer_id'];
        }

        return $resultData;
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
            null => Yii::t('app/superadmin', 'orders.nav.all', [
                'count' => $this->count(),
            ]),
            Orders::STATUS_ADDED => Yii::t('app/superadmin', 'orders.nav.completed', [
                'count' => ArrayHelper::getValue($statusCounters, Orders::STATUS_ADDED, 0)
            ]),
            Orders::STATUS_PAID => Yii::t('app/superadmin', 'orders.nav.ready', [
                'count' => ArrayHelper::getValue($statusCounters, Orders::STATUS_PAID, 0)
            ]),
            Orders::STATUS_PENDING => Yii::t('app/superadmin', 'orders.nav.pending', [
                'count' => ArrayHelper::getValue($statusCounters, Orders::STATUS_PENDING, 0)
            ]),
            Orders::STATUS_ERROR => Yii::t('app/superadmin', 'orders.nav.error', [
                'count' => ArrayHelper::getValue($statusCounters, Orders::STATUS_ERROR, 0)
            ]),
            Orders::STATUS_CANCELED => Yii::t('app/superadmin', 'orders.nav.canceled', [
                'count' => ArrayHelper::getValue($statusCounters, Orders::STATUS_CANCELED, 0)
            ]),
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
            0 => Yii::t('app/superadmin', 'orders.list.item_all', [
                'count' => $this->count($status)
            ]),
            Orders::ITEM_BUY_PANEL => Yii::t('app/superadmin', 'orders.list.item_panels', [
                'count' => ArrayHelper::getValue($itemCounters, Orders::ITEM_BUY_PANEL, 0)
            ]),
            Orders::ITEM_BUY_CHILD_PANEL => Yii::t('app/superadmin', 'orders.list.item_child_panels', [
                'count' => ArrayHelper::getValue($itemCounters, Orders::ITEM_BUY_CHILD_PANEL, 0)
            ]),
            Orders::ITEM_BUY_DOMAIN => Yii::t('app/superadmin', 'orders.list.item_domains', [
                'count' => ArrayHelper::getValue($itemCounters, Orders::ITEM_BUY_DOMAIN, 0)
            ]),
            Orders::ITEM_BUY_SSL => Yii::t('app/superadmin', 'orders.list.item_certificates', [
                'count' => ArrayHelper::getValue($itemCounters, Orders::ITEM_BUY_SSL, 0)
            ]),
            Orders::ITEM_BUY_STORE => Yii::t('app/superadmin', 'orders.list.item_stores', [
                'count' => ArrayHelper::getValue($itemCounters, Orders::ITEM_BUY_STORE, 0)
            ]),
        ];

        return $items;
    }
}
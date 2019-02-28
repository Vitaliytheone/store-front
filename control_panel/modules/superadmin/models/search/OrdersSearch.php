<?php

namespace superadmin\models\search;

use common\models\sommerces\InvoiceDetails;
use common\models\sommerces\Orders;
use control_panel\helpers\DomainsHelper;
use yii\data\Pagination;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use Yii;

/**
 * Class OrdersSearch
 * @package superadmin\models\search
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
        $orders->andWhere([
            'orders.item' => [
                Orders::ITEM_BUY_DOMAIN,
                Orders::ITEM_BUY_SSL,
                Orders::ITEM_PROLONGATION_SSL,
                Orders::ITEM_PROLONGATION_DOMAIN,
                Orders::ITEM_FREE_SSL,
                Orders::ITEM_PROLONGATION_FREE_SSL ,
                Orders::ITEM_BUY_SOMMERCE ,
            ]
        ]);
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
            'customers.email as customer_email',
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
            ->leftJoin(
                'invoice_details', 'invoice_details.item_id = orders.id AND invoice_details.item IN (' . implode(",", InvoiceDetails::getSommerceOrderItems()) . ')'
            )
            ->leftJoin('invoices', 'invoices.id = invoice_details.invoice_id')
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
            $resultData[$key]['date'] = Orders::formatDate($value['date'], 'php:Y-m-d');
            $resultData[$key]['time'] = Orders::formatDate($value['date'], 'php:H:i:s');
            $resultData[$key]['ip'] = $value['ip'];
            $resultData[$key]['domain'] = $value['domain'] ? DomainsHelper::idnToUtf8($value['domain']) : '';
            $resultData[$key]['details'] = $value['details'];
            $resultData[$key]['item'] = Orders::getItemName($value['item']);
            $resultData[$key]['item_id'] = $value['item_id'];
            $resultData[$key]['invoice_id'] = $value['invoice_id'];
            $resultData[$key]['customer_email'] = $value['customer_email'];
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
            Orders::ITEM_BUY_DOMAIN => Yii::t('app/superadmin', 'orders.list.item_domains', [
                'count' => ArrayHelper::getValue($itemCounters, Orders::ITEM_BUY_DOMAIN, 0)
            ]),
            Orders::ITEM_BUY_SSL => Yii::t('app/superadmin', 'orders.list.item_certificates', [
                'count' => ArrayHelper::getValue($itemCounters, Orders::ITEM_BUY_SSL, 0)
            ]),
            Orders::ITEM_BUY_SOMMERCE => Yii::t('app/superadmin', 'orders.list.item_sommerce', [
                'count' => ArrayHelper::getValue($itemCounters, Orders::ITEM_BUY_SOMMERCE, 0)
            ]),
            Orders::ITEM_PROLONGATION_SSL => Yii::t('app/superadmin', 'orders.list.item_prolongation_ssl', [
                'count' => ArrayHelper::getValue($itemCounters, Orders::ITEM_PROLONGATION_SSL, 0)
            ]),
            Orders::ITEM_PROLONGATION_DOMAIN => Yii::t('app/superadmin', 'orders.list.item_prolongation_domain', [
                'count' => ArrayHelper::getValue($itemCounters, Orders::ITEM_PROLONGATION_DOMAIN, 0)
            ]),
            Orders::ITEM_FREE_SSL => Yii::t('app/superadmin', 'orders.list.item_free_ssl', [
                'count' => ArrayHelper::getValue($itemCounters, Orders::ITEM_FREE_SSL, 0)
            ]),
            Orders::ITEM_PROLONGATION_FREE_SSL => Yii::t('app/superadmin', 'orders.list.item_prolongation_free_ssl', [
                'count' => ArrayHelper::getValue($itemCounters, Orders::ITEM_PROLONGATION_FREE_SSL, 0)
            ]),
        ];

        return $items;
    }
}
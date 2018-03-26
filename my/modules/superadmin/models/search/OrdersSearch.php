<?php
namespace my\modules\superadmin\models\search;

use common\models\panels\InvoiceDetails;
use common\models\panels\Orders;
use yii\helpers\ArrayHelper;

/**
 * Class OrdersSearch
 * @package my\modules\superadmin\models\search
 */
class OrdersSearch {

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
     * @return $this
     */
    public function buildQuery($status = null, $item = null)
    {
        $searchQuery = $this->getQuery();

        $orders = Orders::find();

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
        ])->groupBy('orders.id');

        return $orders;
    }

    /**
     * Search panels
     * @return array
     */
    public function search()
    {
        $status = ArrayHelper::getValue($this->params, 'status', null);
        $item = ArrayHelper::getValue($this->params, 'item', null);

        $query = clone $this->buildQuery($status, $item);

        $panels = $query->all();

        return [
            'models' => $panels
        ];
    }

    /**
     * Get count panels by type
     * @param int $status
     * @param int $item
     * @return int
     */
    public function count($status = null, $item = null)
    {
        $query = clone $this->buildQuery($status, $item);

        return $query->count();
    }

    /**
     * Get navs
     * @return array
     */
    public function navs()
    {
        return [
            null => 'All (' . $this->count() . ')',
            Orders::STATUS_ADDED => 'Completed (' . $this->count(Orders::STATUS_ADDED) . ')',
            Orders::STATUS_PAID => 'Ready (' . $this->count(Orders::STATUS_PAID) . ')',
            Orders::STATUS_PENDING => 'Pending (' . $this->count(Orders::STATUS_PENDING) . ')',
            Orders::STATUS_ERROR => 'Error (' . $this->count(Orders::STATUS_ERROR) . ')',
            Orders::STATUS_CANCELED => 'Canceled (' . $this->count(Orders::STATUS_CANCELED) . ')',
        ];
    }

    /**
     * Get aggregated items
     * @return array
     */
    public function getAggregatedItems()
    {
        $status = ArrayHelper::getValue($this->params, 'status', null);
        $items = [
            0 => 'All (' . $this->count($status) . ')',
            Orders::ITEM_BUY_PANEL => 'Panels (' . $this->count($status, Orders::ITEM_BUY_PANEL) . ')',
            Orders::ITEM_BUY_CHILD_PANEL => 'Child Panels (' . $this->count($status, Orders::ITEM_BUY_CHILD_PANEL) . ')',
            Orders::ITEM_BUY_DOMAIN => 'Domains (' . $this->count($status, Orders::ITEM_BUY_DOMAIN) . ')',
            Orders::ITEM_BUY_SSL => 'Certificates (' . $this->count($status, Orders::ITEM_BUY_SSL) . ')',
        ];

        return $items;
    }
}
<?php
namespace sommerce\models\search;

use common\models\store\Orders;
use common\models\store\Packages;
use common\models\store\Suborders;
use common\models\stores\Stores;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * Class OrdersSearch
 * @package sommerce\models\search
 */
class OrdersSearch {

    /**
     * @var Stores
     */
    protected $_store;


    /**
     * @var Orders
     */
    protected $_order = [];

    /**
     * Packages list
     * @var array
     */
    protected static $_packages;

    /**
     * @var int - total price
     */
    protected static $_total = 0;

    /**
     * @var array - items
     */
    protected static $_items;

    /**
     * Set store
     * @param Stores $store
     * @return static
     */
    public function setStore($store)
    {
        $this->_store = $store;
        return $this;
    }

    /**
     * Set order
     * @param Orders $order
     */
    public function setOrder(Orders $order)
    {
        $this->_order = $order;
    }

    /**
     * Get cart total price
     * @return float|integer
     */
    public function getTotal()
    {
        if (static::$_total) {
            return static::$_total;
        }

        $this->search();

        return static::$_total;
    }

    /**
     * Get cart count items
     * @return integer
     */
    public function getCount()
    {
        if (null !== static::$_items) {
            return count(static::$_items);
        }

        $this->search();

        return count(static::$_items);
    }

    /**
     * Build sql query
     * @return Query
     */
    public function buildQuery()
    {
        $query = (new Query())
            ->select([
                'id',
                'link',
                'quantity',
                'amount',
                'package_id',
                'status'
            ])
            ->from($this->_store->db_name . '.' . Suborders::tableName())
            ->andWhere([
                'order_id' => $this->_order->id
            ]);

        return $query;
    }

    public function search()
    {
        if (null === static::$_items) {
            $items = $this->buildQuery()
                ->orderBy([
                    'id' => SORT_DESC
                ])
                ->all();

                $this->prepareItems($items);
        }

        return [
            'models' => static::$_items,
        ];
    }

    /**
     * Get packages
     * @return array
     */
    public function getPackages()
    {
        if (null !== static::$_packages) {
            return static::$_packages;
        }

        static::$_packages = (new Query())
            ->select([
                'id',
                'name',
                'price',
                'quantity'
            ])
            ->from($this->_store->db_name . '.packages')
            ->andWhere([
                'visibility' => Packages::VISIBILITY_YES,
                'deleted' => Packages::DELETED_NO
            ])
            ->all();

        static::$_packages = ArrayHelper::index(static::$_packages, 'id');

        return static::$_packages;
    }

    /**
     * Prepare order items
     * @param array $items
     * @return array
     */
    public function prepareItems($items)
    {
        $packages = $this->getPackages();

        static::$_items = [];
        static::$_total = 0;

        foreach ($items as $item) {
            $package = ArrayHelper::getValue($packages, $item['package_id']);

            static::$_total += $item['amount'];

            static::$_items[] = [
                'id' => $item['id'],
                'name' => ArrayHelper::getValue($package, 'name'),
                'link' => $item['link'],
                'quantity' => $item['quantity'],
                'price' => $item['amount'],
                'status' => Suborders::getStatusName($item['status'], false),
            ];
        }

        return static::$_items;
    }
}
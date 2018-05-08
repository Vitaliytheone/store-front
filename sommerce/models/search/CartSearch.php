<?php
namespace sommerce\models\search;

use common\helpers\PriceHelper;
use common\models\store\Packages;
use common\models\stores\Stores;
use sommerce\helpers\UserHelper;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * Class CartSearch
 * @package sommerce\models\search
 */
class CartSearch {

    /**
     * @var Stores
     */
    protected $_store;


    /**
     * @var array
     */
    protected $_keys = [];

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

    public function __construct()
    {
        $this->_keys = UserHelper::getCartKeys();
    }

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
     * Set cart keys
     * @param array $keys
     */
    public function setKeys(array $keys)
    {
        $this->_keys = $keys;
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
                'c.id',
                'c.key',
                'c.link',
                'c.package_id',
                'c.created_at'
            ])
            ->from($this->_store->db_name . '.carts c')
            ->andWhere([
                'c.key' => $this->_keys
            ]);

        return $query;
    }

    public function search()
    {
        if (null === static::$_items) {
            $items = $this->buildQuery()
                ->orderBy([
                    'c.created_at' => SORT_DESC
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
     * Return Items with renamed fields
     * @return array
     */
    public function getItemsForView()
    {
        if (null === static::$_items) {
            $this->search();
        }

        $itemNo = 1;

        $items = static::$_items;

        foreach ($items as &$item) {

            $item['id'] = $itemNo;
            $item['name'] = $item['package_name'];
            $item['quantity'] = $item['package_quantity'];

            unset($item['package_id']);
            unset($item['package_name']);
            unset($item['package_quantity']);

            $itemNo++;
        }

        return $items;
    }

    /**
     * Prepare cart items
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

            if (empty($package)) {
                continue;
            }

            static::$_total += $package['price'];

            static::$_items[] = [
                'id' => $item['id'],
                'key' => $item['key'],
                'price' => $package['price'],
                'package_id' => $item['package_id'],
                'created' => $item['created_at'],
                'package_quantity' => $package['quantity'],
                'package_name' => $package['name'],
                'link' => $item['link'],
            ];
        }

        return static::$_items;
    }
}
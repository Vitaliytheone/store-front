<?php
namespace frontend\models\search;

use common\helpers\PriceHelper;
use common\models\store\Packages;
use common\models\stores\Stores;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * Class CartSearch
 * @package frontend\models\search
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
    protected $_total = 0;

    /**
     * Set store
     * @param Stores $store
     */
    public function setStore($store)
    {
        $this->_store = $store;
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
        return $this->_total;
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
        $query = clone $this->buildQuery();

        $items = $query
            ->orderBy([
                'id' => SORT_DESC
            ])
            ->all();

        $items = $this->prepareItems($items);

        return [
            'models' => $items,
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
                'price'
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
     * Prepare cart items
     * @param array $items
     * @return array
     */
    public function prepareItems($items)
    {
        $returnItems = [];
        $packages = $this->getPackages();

        foreach ($items as $item) {
            $package = ArrayHelper::getValue($packages, $item['package_id']);

            if (empty($package)) {
                continue;
            }

            $this->_total += $package['price'];

            $returnItems[] = [
                'id' => $item['id'],
                'key' => $item['key'],
                'link' => $item['link'],
                'price' => PriceHelper::prepare($package['price'], $this->_store->currency),
                'package_name' => $package['name'],
                'created' => $item['created_at'],
            ];
        }

        return $returnItems;
    }
}
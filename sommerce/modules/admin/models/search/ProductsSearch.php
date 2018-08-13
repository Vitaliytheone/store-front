<?php

namespace sommerce\modules\admin\models\search;

use common\models\panels\AdditionalServices;
use common\models\stores\StoreProviders;
use common\models\stores\Stores;
use yii;
use yii\db\Query;
use yii\base\Model;
use common\models\store\Products;
use common\models\store\Packages;
use yii\helpers\ArrayHelper;

/**
 * Class ProductsSearch
 * @package sommerce\modules\admin\models\search
 */
class ProductsSearch extends Model
{
    private $_db;
    private $_productsTable;
    private $_packagesTable;
    private $_providersTable;


    /**
     * Cached Store providers
     * @var array
     */
    private $_store_providers;

    /** @inheritdoc */
    public function init()
    {
        $this->_db = yii::$app->store->getInstance()->db_name;
        $this->_productsTable = $this->_db . "." . Products::tableName();
        $this->_packagesTable = $this->_db . "." . Packages::tableName();
        $this->_providersTable = AdditionalServices::tableName();

        parent::init();
    }

    /**
     * Return store providers
     * @return array
     */
    public function getStoreProviders()
    {
        if (!$this->_store_providers) {
            /** @var Stores $store */
            $store = Yii::$app->store->getInstance();

            $this->_store_providers = (new Query())
                ->select([
                    'pr.res as id', 'pr.name as site',
                    'sp.store_id'
                ])
                ->from(['sp' => StoreProviders::tableName()])
                ->where(['sp.store_id' => $store->id])
                ->leftJoin(['pr' => AdditionalServices::tableName()], 'pr.res = sp.provider_id')
                ->indexBy('id')
                ->all();
        }

        return $this->_store_providers;
    }


    /**
     * Return Products & Packages
     * @return array
     */
    public function getProductsPackages()
    {
        $productsRows = (new Query())
            ->select([
                'pr.id pr_id', 'pr.name pr_name', 'pr.position pr_position', 'pr.visibility pr_visibility',
                'pk.id pk_id', 'pk.product_id pk_pr_id', 'pk.name pk_name', 'pk.position pk_position', 'pk.visibility pk_visibility', 'pk.mode pk_mode', 'pk.price pk_price', 'pk.quantity pk_quantity', 'pk.deleted pk_deleted',
                'pk.provider_id'
            ])
            ->from("$this->_productsTable pr")
            ->leftJoin("$this->_packagesTable pk", 'pk.product_id = pr.id AND pk.deleted = :deleted', [':deleted' => Packages::DELETED_NO])
            ->orderBy(['pr.position' => SORT_ASC, 'pk.position' => SORT_ASC])
            ->all();

        $providers = $this->getStoreProviders();

        // Make products packages
        $productIds = array_unique(array_column($productsRows, 'pr_id'));
        $productsPackages = [];

        foreach ($productIds as $productId) {

            // Make product`s packages
            $productPackages = array_filter($productsRows, function($productRow) use ($productId){
                return $productId == $productRow['pk_pr_id'];
            });
            array_walk($productPackages, function (&$package, $key) use ($providers) {

                $provider = ArrayHelper::getValue($providers, $package['provider_id'] . '.site', '');

                $package = [
                    'id' => $package['pk_id'],
                    'product_id' => $package['pr_id'],
                    'name' => $package['pk_name'],
                    'position' => $package['pk_position'],
                    'visibility' => $package['pk_visibility'],
                    'mode' => $package['pk_mode'],
                    'price' => $package['pk_price'],
                    'quantity' => $package['pk_quantity'],
                    'provider' => $provider,
                    'deleted' => $package['pk_deleted'],
                ];
            });

            // Make product
            $currentProductKey = array_search($productId, array_column($productsRows, 'pr_id'));
            $currentRow = $productsRows[$currentProductKey];
            $productsPackages[$productId] = [
                'id' => $productId,
                'name' => $currentRow['pr_name'],
                'position' => $currentRow['pr_position'],
                'visibility' => $currentRow['pr_visibility'],
                'packages' => $productPackages,
            ];
        }

        return $productsPackages;
    }

    /**
     * Return products-properties list
     * @return array
     */
    public function getProductsProperties()
    {
        $products = (new Query())
            ->select(['id', 'name', 'properties'])
            ->from($this->_productsTable)
            ->where([
                'not',
                ['properties' => null]
            ])
            ->all();

        array_walk($products, function(&$product) {
            $product['properties'] = json_decode($product['properties'], true);
        });

        return $products;
    }
}
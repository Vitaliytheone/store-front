<?php

namespace frontend\modules\admin\models\search;

use yii;
use yii\db\Query;
use common\helpers\DbHelper;
use yii\base\Model;
use common\models\store\Products;
use common\models\store\Packages;
use common\models\stores\Providers;

/**
 * Class ProductsSearch
 * @package frontend\modules\admin\models\search
 */
class ProductsSearch extends Model
{
    private $_db;
    private $_productsTable;
    private $_packagesTable;
    private $_providersTable;

    public function init()
    {
        $this->_db = yii::$app->store->getInstance()->db_name;
        $this->_productsTable = $this->_db . "." . Products::tableName();
        $this->_packagesTable = $this->_db . "." . Packages::tableName();
        $this->_providersTable = Providers::tableName();

        parent::init();
    }


    /**
     * Return Products & Packages
     * @return array
     */
    public function getProductsPackages()
    {
        $storeDb = yii::$app->store->getInstance()->db_name;
        $storesDb = DbHelper::getDsnAttribute('name', yii::$app->getDb());

        $productsRows = (new Query())
            ->select([
                'pr.id pr_id', 'pr.name pr_name', 'pr.position pr_position', 'pr.visibility pr_visibility',
                'pk.id pk_id', 'pk.product_id pk_pr_id', 'pk.name pk_name', 'pk.position pk_position', 'pk.visibility pk_visibility', 'pk.mode pk_mode', 'pk.price pk_price', 'pk.quantity pk_quantity', 'pk.deleted pk_deleted',
                'prv.site'
            ])
            ->from("$this->_productsTable pr")
            ->leftJoin("$this->_packagesTable pk", 'pk.product_id = pr.id AND pk.deleted = :deleted', [':deleted' => Packages::DELETED_NO])
            ->leftJoin("$this->_providersTable prv", 'prv.id = pk.provider_id')
            ->orderBy(['pr.position' => SORT_ASC, 'pk.position' => SORT_ASC])
            ->all();

        // Make products packages
        $productIds = array_unique(array_column($productsRows, 'pr_id'));
        $productsPackages = [];
        foreach ($productIds as $productId) {

            // Make product`s packages
            $productPackages = array_filter($productsRows, function($productRow) use ($productId){
                return $productId == $productRow['pk_pr_id'];
            });
            array_walk($productPackages, function (&$package, $key) {
                $package = [
                    'id' => $package['pk_id'],
                    'product_id' => $package['pr_id'],
                    'name' => $package['pk_name'],
                    'position' => $package['pk_position'],
                    'visibility' => $package['pk_visibility'],
                    'mode' => $package['pk_mode'],
                    'price' => $package['pk_price'],
                    'quantity' => $package['pk_quantity'],
                    'provider' => $package['site'],
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
}
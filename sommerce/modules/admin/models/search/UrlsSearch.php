<?php

namespace sommerce\modules\admin\models\search;

use common\models\sommerce\Pages;
use common\models\sommerce\Products;
use common\models\sommerces\PaymentMethods;
use common\models\sommerces\Stores;
use yii\base\Model;
use yii\db\Query;

class UrlsSearch extends Model
{
    private $_storeDb;
    private $_productsTable;
    private $_pagesTable;
    private $_paymentsTable;

    /**
     * @param Stores $store
     */
    public function setStore(Stores $store)
    {
        $this->_storeDb = $store->db_name;
        $this->_productsTable = $this->_storeDb . '.' . Products::tableName();
        $this->_pagesTable = $this->_storeDb . '.' . Pages::tableName();
        $this->_paymentsTable = PaymentMethods::tableName();
    }

    /**
     * Return union array of exiting Pages and Products urls
     * @return array
     */
    public function searchUrls()
    {
        $productUrls = (new Query())
            ->select('url')
            ->from($this->_productsTable);

        $pageUrls = (new Query())
            ->select('url')
            ->from($this->_pagesTable);

        $paymentsUrls = (new Query())
            ->select('url')
            ->from($this->_paymentsTable);

        return $productUrls->union($pageUrls)->union($paymentsUrls)->column();
    }
}
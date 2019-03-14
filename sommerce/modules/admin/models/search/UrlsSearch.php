<?php

namespace sommerce\modules\admin\models\search;

use common\models\sommerce\Pages;
use common\models\sommerces\PaymentMethods;
use common\models\sommerces\Stores;
use yii\base\Model;
use yii\db\Query;

class UrlsSearch extends Model
{
    private $_storeDb;
    private $_pagesTable;
    private $_paymentsTable;

    /**
     * @param Stores $store
     */
    public function setStore(Stores $store)
    {
        $this->_storeDb = $store->db_name;
        $this->_pagesTable = $this->_storeDb . '.' . Pages::tableName();
        $this->_paymentsTable = PaymentMethods::tableName();
    }

    /**
     * Return union array of exiting Pages and Payments urls
     * @return array
     */
    public function searchUrls(): array
    {
        $pageUrls = (new Query())
            ->select('url')
            ->from($this->_pagesTable);

        $paymentsUrls = (new Query())
            ->select('url')
            ->from($this->_paymentsTable);

        return $pageUrls->union($paymentsUrls)->column();
    }
}
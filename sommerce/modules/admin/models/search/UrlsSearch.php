<?php

namespace sommerce\modules\admin\models\search;

use common\models\store\Pages;
use common\models\store\Products;
use Yii;
use yii\base\Model;
use yii\db\Query;

class UrlsSearch extends Model
{
    private $_storeDb;
    private $_productsTable;
    private $_pagesTable;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->_storeDb = Yii::$app->store->getInstance()->db_name;
        $this->_productsTable = $this->_storeDb . "." . Products::tableName();
        $this->_pagesTable = $this->_storeDb . "." . Pages::tableName();

        parent::init();
    }

    /**
     * Return union array of exiting Pages and Products urls
     * @return array
     */
    public function searchUrls()
    {
        $productUrls = (new Query())
            ->select("url")
            ->from($this->_productsTable);

        $pageUrls = (new Query())
            ->select("url")
            ->from($this->_pagesTable)
            ->where(['deleted' => Pages::DELETED_NO]);

        return $productUrls->union($pageUrls)->column();
    }
}
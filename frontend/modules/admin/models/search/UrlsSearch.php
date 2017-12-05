<?php

namespace frontend\modules\admin\models\search;

use Yii;
use yii\base\Model;
use yii\db\Query;

class UrlsSearch extends Model
{
    private $_storeDb;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->_storeDb = Yii::$app->store->getInstance()->db_name;
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
            ->from("$this->_storeDb.products");

        $pageUrls = (new Query())
            ->select("url")
            ->from("$this->_storeDb.pages");

        return $productUrls->union($pageUrls)->column();
    }
}
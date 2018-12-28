<?php
namespace admin\models\search;

use common\models\gateway\Pages;
use yii\db\Query;

/**
 * Class UrlsSearch
 * @package admin\models\search
 */
class UrlsSearch extends BaseSearch
{
    /**
     * Return union array of exiting Pages and Products urls
     * @return array
     */
    public function search()
    {
        $pageUrls = (new Query())
            ->select("url")
            ->from($this->_gateway->db_name . '.' . Pages::tableName())
            ->where(['deleted' => Pages::DELETED_NO]);

        return $pageUrls->column();
    }
}
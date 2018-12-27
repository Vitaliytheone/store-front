<?php

namespace admin\models\search;

use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use common\models\gateway\Pages;

/**
 * Class PagesSearch
 * @package admin\models\search
 */
class PagesSearch extends BaseSearch
{
    /**
     * Return array of Pages data
     * @return array
     */
    public function search()
    {
        $pages = (new Query())
            ->select(['id', 'title', 'visibility', 'content', 'seo_title', 'seo_description', 'url', 'created_at', 'updated_at', 'is_default'])
            ->from($this->_gateway->db_name . '.' . Pages::tableName())
            ->where(['deleted' => Pages::DELETED_NO])
            ->indexBy('id')
            ->orderBy(['id' => SORT_DESC])
            ->all();


        // Populate by additional data
        array_walk($pages, function(&$page){

            $dtUpdated = ArrayHelper::getValue($page, 'updated_at', null);


            $page['visibility_title'] = $page['visibility']|0 ? Yii::t('admin', 'settings.pages_visibility_visible') : Yii::t('admin', 'settings.pages_visibility_hidden');
            $page['updated_at_formatted'] = $dtUpdated ? Yii::$app->formatter->asDatetime($dtUpdated, 'yyyy-MM-dd HH:mm:ss') : Yii::t('admin', 'settings.pages_update_never');
        });

        return $pages;
    }
}
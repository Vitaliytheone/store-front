<?php

namespace sommerce\modules\admin\models\search;

use common\models\store\Pages;
use common\models\stores\Stores;
use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * Class PagesSearch
 * @package sommerce\modules\admin\models\search
 */
class PagesSearch extends Pages
{
    private $_db;
    private $_pagesTable;

    /**
     * @param Stores $store
     */
    public function setStore(Stores $store)
    {
        $this->_db = $store->db_name;
        $this->_pagesTable = $this->_db . "." . Pages::tableName();
    }

    /**
     * Return array of Pages data
     * @return array
     */
    public function searchPages()
    {
        $pages = (new Query())
            ->select(['id', 'visibility', 'name', 'is_draft', 'seo_title', 'seo_description', 'seo_keywords', 'url', 'created_at', 'updated_at'])
            ->from($this->_pagesTable)
            ->indexBy('id')
            ->orderBy(['id' => SORT_DESC])
            ->all();


        // Populate by additional data
        array_walk($pages, function(&$page){

            $dtUpdated = ArrayHelper::getValue($page, 'updated_at', null);
            $page['can_delete'] = static::canDelete($page);
            $page['is_draft'] ?  $page['status'] = Yii::t('admin', 'pages.status.draft') : $page['status'] = '';

            $page['is_draft'] ?  $page['status'] = Yii::t('admin', 'pages.status.draft') : $page['status'] = '';
            $page['updated_at_formatted'] = $dtUpdated ? Yii::$app->formatter->asDatetime($dtUpdated, 'yyyy-MM-dd HH:mm:ss') : Yii::t('admin', 'settings.pages_update_never');
        });

        return $pages;
    }

    /**
     * @param array|Pages $page
     * @return bool
     */
    public static function canDelete($page) {

        $except = [
            'home'
        ];

        return !ArrayHelper::isIn($page['url'], $except);
    }
}
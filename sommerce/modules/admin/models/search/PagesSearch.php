<?php

namespace sommerce\modules\admin\models\search;

use Yii;
use common\models\store\Pages;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class PagesSearch extends Pages
{
    private $_db;
    private $_pagesTable;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->_db = Yii::$app->store->getInstance()->db_name;
        $this->_pagesTable = $this->_db . "." . Pages::tableName();

        parent::init();
    }

    /**
     * Return array of Pages data
     * @return array
     */
    public function searchPages()
    {
        $pages = (new Query())
            ->select(['id', 'title', 'visibility', 'content', 'seo_title', 'seo_description', 'url', 'created_at', 'updated_at'])
            ->from($this->_pagesTable)
            ->where(['deleted' => self::DELETED_NO])
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
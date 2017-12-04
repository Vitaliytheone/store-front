<?php

namespace frontend\modules\admin\models\search;

use Yii;
use common\models\store\Pages;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class PagesSearch extends Pages
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
     * Return array of Pages data
     * @return array
     */
    public function searchPages()
    {
        $pages = (new Query())
            ->select(['id', 'name', 'visibility', 'content', 'seo_title', 'seo_description', 'url', 'created_at', 'updated_at'])
            ->from("$this->_storeDb.pages")
            ->where(['deleted' => self::DELETED_NO])
            ->indexBy('id')
            ->orderBy(['id' => SORT_DESC])
            ->all();

        // Populate by additional data
        array_walk($pages, function(&$page){
            $page['visibility_title'] = $page['visibility']|0 ? Yii::t('admin', 'settings.pages_visibility_visible') : Yii::t('admin', 'settings.pages_visibility_hidden');
            $page['updated_at_formatted'] = Yii::$app->formatter->asDatetime(ArrayHelper::getValue($page, 'updated_at'), 'yyyy-MM-dd HH:mm:ss');
        });

        return $pages;
    }
}
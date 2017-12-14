<?php

namespace frontend\modules\admin\models\search;

use common\models\store\Navigations;
use Yii;
use yii\base\Model;
use yii\db\Query;

class NavigationsSearch extends Model
{
    private $_storeDb;
    private $_navigationsTable;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->_storeDb = Yii::$app->store->getInstance()->db_name;
        $this->_navigationsTable = $this->_storeDb . "." . Navigations::tableName();

        parent::init();
    }

    /**
     * Return all non deleted navigation items
     * @return array
     */
    public function search()
    {
        return (new Query())
            ->select(['id', 'parent_id', 'name', 'link', 'link_id', 'position', 'url'])
            ->from($this->_navigationsTable)
            ->where(['deleted' => Navigations::DELETED_NO])
            ->orderBy(['parent_id' => SORT_ASC, 'position' => SORT_ASC])
            ->indexBy('id')
            ->all();
    }

    /**
     * Return navigation items tree
     * @return array
     */
    public function getTree()
    {
        $list = $this->search();
        $tree = [];

        foreach ($list as $id => &$node) {
            // Is root
            if (!$node['parent_id']){
                $tree[$id] = &$node;
            }else{
                // Is node
                $list[$node['parent_id']]['nodes'][$id] = &$node;
            }
        }

        return $tree;
    }

}
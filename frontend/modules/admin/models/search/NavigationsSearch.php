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

    /**
     * Return all children ids of tree node
     * @param $parentId
     * @return array
     */
    public static function getChildrenTreeNodeIds($parentId)
    {
        $db = Yii::$app->storeDb;
        $table = Navigations::tableName();

        /** @var \yii\db\Connection $db */
        $query = $db->createCommand("
            SELECT GROUP_CONCAT(children_ids SEPARATOR ',')
            FROM (
                SELECT @pv:=(SELECT GROUP_CONCAT(`id` SEPARATOR ',') FROM $table WHERE `parent_id` IN (@pv)) AS children_ids FROM $table
                JOIN
                (SELECT @pv:= :parentId) tmp
                WHERE `parent_id` IN (@pv)
            ) v_table;
        ")
            ->bindValue(':parentId', $parentId)
            ->queryColumn();

        $ids = [];

        if ($query[0]) {
            $ids = explode(',', $query[0]);
        }

        return $ids;
    }

}
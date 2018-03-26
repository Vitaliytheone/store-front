<?php

namespace sommerce\models\search;

use common\models\store\Navigation;
use Yii;
use yii\base\Model;
use yii\db\Connection;
use yii\db\Query;


class NavigationSearch extends Model
{
    private $_storeDb;
    private $_navigationsTable;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->_storeDb = Yii::$app->store->getInstance()->db_name;
        $this->_navigationsTable = $this->_storeDb . "." . Navigation::tableName();

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
            ->where([
                'deleted' => Navigation::DELETED_NO
            ])
            ->orderBy([
                'parent_id' => SORT_ASC,
                'position' => SORT_ASC
            ])
            ->indexBy('id')
            ->all();
    }

    /**
     * Return navigation items tree
     * @return array
     */
    public function getTree()
    {
        $list = array_map(function ($menuItem) {
            $menuItem['url'] = '/' . rtrim($menuItem['url'], '/');
            return $menuItem;
        }, $this->search());

        $tree = [];

        foreach ($list as $id => &$node) {
            // Is root
            if (!$node['parent_id']) {
                $tree[$id] = &$node;
            } else {
                // Is node
                $list[$node['parent_id']]['nodes'][$id] = &$node;
            }
        }

        return $tree;
    }

    /**
     * Return sommerce user menu tree
     * Allowed fields are:
     *  'active'    0|1,
     *  'link'      string,
     *  'name'      string,
     *  'submenu'   array|false
     *
     * @param $currentUrl
     * @return array
     */
    public function getSiteMenuTree($currentUrl = false)
    {
        $list = $this->search();

        /**
         * Find root menu item id from flat-tree by children id
         * @param $activeId
         * @return mixed
         */
        $getRootItemId = function($activeId) use ($list) {
            $currentId = $activeId;
            $currentItem = $list[$currentId];

            // Iteration limitation
            $iterationLimit = 4;
            $iterationCount = 0;

            while ((int)$currentItem['parent_id'] !== 0 && $iterationCount < $iterationLimit) {
                $currentId = $currentItem['parent_id'];
                $currentItem = $list[$currentId];

                $iterationCount++;
            };

            return $currentId;
        };

        // Additional params
        foreach ($list as $id => &$item) {
            $url = null;

            // Make Url for items without children and '#' for items with children
            if (array_search($id, array_column($list, 'parent_id')) === false) {
                $url = (int)$item['link'] === Navigation::LINK_WEB_ADDRESS ? $item['url'] : '/' . trim($item['url'], '/');
            } else {
                $url = '#';
            }


            // Make Active
            $currentUrl = $currentUrl === '/index' ? '/' : $currentUrl;
            $active = $url === $currentUrl ? 1 : 0;

            $item['link'] = $url;
            $item['active'] = $active;

            if ($active) {
                // Set root menu item active
                $itemRootId = $getRootItemId($id);
                $list[$itemRootId]['active'] = $active;
            }
        }

        // Make menu tree
        $tree = [];
        foreach ($list as $id => &$node) {

            // Make tree
            if ((int)$node['parent_id'] === 0) {
                $tree[$id] = &$node;
            } else {
                // Is node
                $list[$node['parent_id']]['submenu'][$id] = &$node;
            }

            // Set submenu = false is empty
            if (!isset($node['submenu'])) {
                $node['submenu'] = false;
            }

            // Cleanup unneeded
            unset($node['id']);
            unset($node['parent_id']);
            unset($node['link_id']);
            unset($node['position']);
            unset($node['url']);
        }

        return $tree;
    }

    /**
     * Return first-level children ids of the parent id node
     * @param $parentId
     * @return array
     */
    public static function getFirstLevelChildrenIds($parentId)
    {
        $db = Yii::$app->storeDb;
        $table = Navigation::tableName();

        /** @var Connection $db */
        $query = $db->createCommand("
            SELECT `id`
            FROM $table
            WHERE `parent_id` = :parentId
            ORDER BY `position` ASC
        ")
            ->bindValue(':parentId', $parentId)
            ->queryColumn();

        return $query;
    }

}
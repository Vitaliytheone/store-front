<?php

namespace frontend\modules\admin\models\forms;

use common\models\store\Navigation;
use yii\helpers\ArrayHelper;

/**
 * Class UpdatePositionsNavigationForm
 * @package frontend\modules\admin\models\forms
 */
class UpdatePositionsNavigationForm extends Navigation
{
    /**
     * Batch update Navigation item positions
     * @param $postData
     * @return bool|int
     */
    public function updatePositions($postData)
    {
        $positionsTree = ArrayHelper::getValue($postData, 'positions', null);

        if (!$positionsTree) {
            return false;
        }

        $positions = static::flatten($positionsTree);

        $positionsImploded = null;
        foreach ($positions as $position) {
            $id = $position['id']|0;
            $parentId = $position['parent_id']|0;
            $position = $position['position']|0;
            $positionsImploded = ($positionsImploded ? $positionsImploded . ',' : '') . "('$id', '$parentId', '$position')";
        }

        $navigationTable = static::tableName();
        $command = static::getDb()
            ->createCommand("
                INSERT INTO $navigationTable (id, parent_id, position)
                VALUES $positionsImploded
                ON DUPLICATE KEY UPDATE
                parent_id=VALUES(parent_id), position=VALUES(position)
        ")->execute();

        return true;
    }

    /**
     * Flatten position tree array
     * @param $tree
     * @param int $parentId
     * @return array
     */
    private static function flatten($tree, $parentId = 0)
    {
        $flatArray = [];

        foreach ($tree as $position => $node) {
            if (array_key_exists('children', $node)) {

                $flatArray[] = [
                    'id' => $node['id'],
                    'position' => $position,
                    'parent_id' => $parentId,
                ];

                $children = static::flatten($node['children'], $node['id']);
                $flatArray = array_merge($flatArray, $children);
                unset($node['children']);

            } else {
                $flatArray[] = [
                    'id' => $node['id'],
                    'position' => $position,
                    'parent_id' => $parentId,
                ];
            }
        }

        return $flatArray;
    }

}
<?php

namespace my\modules\superadmin\models\search;
use common\models\panels\Project;
use my\helpers\SpecialCharsHelper;
use yii\db\Query;


/**
 * Class SubscriptionSearch
 * @package my\modules\superadmin\models\search
 */
class SubscriptionSearch
{

    use SearchTrait;

    /**
     * Get models array
     * @return array
     */
    public function search()
    {
        $currentTime = time();

        $models = (new Query())
            ->select([
                'id',
                'site as panel',
                'db',
            ])
            ->from('project')
            ->where(['act' => Project::STATUS_ACTIVE])
            ->andWhere('db != ""')
            ->all();

        foreach ($models as $key => $model) {
            $counts = (new Query())
                ->select('COUNT(id) as allCount')
                ->from($model['db'] . '.auto_orders')
                ->all();

            $activeCount = (new Query())
                ->select([
                    'COUNT(auto_orders.id) as activeCount',
                    'AVG('.$currentTime.' - avg.updated_at) as avg'
                    ])
                ->from($model['db'] . '.auto_orders')
                ->leftJoin($model['db'] . '.auto_orders as avg', 'avg.id = auto_orders.id AND avg.updated_at > 0')
                ->where(['auto_orders.status' => 1])
                ->all();

            $pausedCount = (new Query())
                ->select('COUNT(id) as pausedCount')
                ->from($model['db'] . '.auto_orders')
                ->where(['status' => 2])
                ->all();

            $completedCount = (new Query())
                ->select('COUNT(id) as completedCount')
                ->from($model['db'] . '.auto_orders')
                ->where(['status' => 3])
                ->all();

            $expiredCount = (new Query())
                ->select('COUNT(id) as expiredCount')
                ->from($model['db'] . '.auto_orders')
                ->where(['status' => 5])
                ->all();

            $canceledCount = (new Query())
                ->select('COUNT(id) as canceledCount')
                ->from($model['db'] . '.auto_orders')
                ->where(['status' => 4])
                ->all();

            $models[$key]['allCount'] = $counts[0]['allCount'];
            $models[$key]['activeCount'] = $activeCount[0]['activeCount'];
            $models[$key]['pausedCount'] = $pausedCount[0]['pausedCount'];
            $models[$key]['completedCount'] = $completedCount[0]['completedCount'];
            $models[$key]['expiredCount'] = $expiredCount[0]['expiredCount'];
            $models[$key]['canceledCount'] = $canceledCount[0]['canceledCount'];
            $models[$key]['avg'] = $activeCount[0]['avg'];

        }

        return $models;
    }
}

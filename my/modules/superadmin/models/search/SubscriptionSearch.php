<?php

namespace superadmin\models\search;
use common\models\panels\Project;
use my\helpers\SpecialCharsHelper;
use yii\db\Query;


/**
 * Class SubscriptionSearch
 * @package superadmin\models\search
 */
class SubscriptionSearch
{

    const AUTO_ORDERS_STATUS_ACTIVE = 1;
    const AUTO_ORDERS_STATUS_PAUSED = 2;
    const AUTO_ORDERS_STATUS_COMPLETED = 3;
    const AUTO_ORDERS_STATUS_CANCELED = 4;
    const AUTO_ORDERS_STATUS_EXPIRED = 5;

    use SearchTrait;

    /**
     * Get models array
     * @return array
     */
    public function search(): array
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

        $result = [];

        foreach ($models as $key => $model) {
            $allCount = 0;

            $countQuery = (new Query())
                ->select([
                    'auto_orders.status',
                    'COUNT(auto_orders.id) as count',
                    'AVG(' . $currentTime . ' - updated_date.updated_at) as avg',
                ])
                ->from('`' . $model['db'] . '`.auto_orders')
                ->leftJoin(
                    '`' . $model['db'] . '`.auto_orders as updated_date',
                    'updated_date.id = auto_orders.id AND updated_date.updated_at > 0 AND updated_date.status = ' . static::AUTO_ORDERS_STATUS_ACTIVE
                )
                ->where(['auto_orders.not_checked' => 0])
                ->groupBy('auto_orders.status')
                ->all();

            if (!empty($countQuery)) {
                // Set default values
                $result[$key] = $model;
                $result[$key]['allCount'] = 0;
                $result[$key]['activeCount'] = 0;
                $result[$key]['pausedCount'] = 0;
                $result[$key]['completedCount'] = 0;
                $result[$key]['expiredCount'] = 0;
                $result[$key]['canceledCount'] = 0;
                $result[$key]['avg'] = 0;

                for ($i = 0; $i < count($countQuery); $i++) {
                    $allCount += $countQuery[$i]['count'];
                }

                foreach ($countQuery as $value) {
                    $result[$key]['allCount'] = $allCount;
                    $result[$key]['activeCount'] = $value['status'] == static::AUTO_ORDERS_STATUS_ACTIVE ? $value['count'] : $result[$key]['activeCount'];
                    $result[$key]['pausedCount'] = $value['status'] == static::AUTO_ORDERS_STATUS_PAUSED ? $value['count'] : $result[$key]['pausedCount'];
                    $result[$key]['completedCount'] = $value['status'] == static::AUTO_ORDERS_STATUS_COMPLETED ? $value['count'] : $result[$key]['completedCount'];
                    $result[$key]['expiredCount'] = $value['status'] == static::AUTO_ORDERS_STATUS_EXPIRED ? $value['count'] : $result[$key]['expiredCount'];
                    $result[$key]['canceledCount'] = $value['status'] == static::AUTO_ORDERS_STATUS_CANCELED ? $value['count'] : $result[$key]['canceledCount'];
                    $result[$key]['avg'] = !empty($countQuery[0]['avg']) ? round($countQuery[0]['avg']) : $result[$key]['avg'];
                }
            }
        }

        return [
            'models' => $result,
            'totals' => $this->getTotals($result),
        ];
    }

    /**
     * @param array $models
     * @return array
     */
    private function getTotals(array $models): array
    {
        $totals = [
            'all' => 0,
            'active' => 0,
            'paused' => 0,
            'completed' => 0,
            'expired' => 0,
            'canceled' => 0,
            'avg' => 0,
        ];

        if (count($models) == 0) {
            return $totals;
        }

        $fieldCount = 0;

        foreach ($models as $model) {
            $totals['all'] += $model['allCount'];
            $totals['active'] += $model['activeCount'];
            $totals['paused'] += $model['pausedCount'];
            $totals['completed'] += $model['completedCount'];
            $totals['expired'] += $model['expiredCount'];
            $totals['canceled'] += $model['canceledCount'];
            $totals['avg'] += $model['avg'];

            $fieldCount++;
        }

        $totals['avg'] = round($totals['avg'] / $fieldCount);

        return $totals;
    }
}

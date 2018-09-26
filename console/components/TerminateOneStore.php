<?php

namespace console\components;

use common\models\stores\Stores;
use common\models\common\ProjectInterface;
use common\models\panels\Logs;
use yii\db\ActiveRecord;
use yii\db\Exception as DbException;
use Yii;

class TerminateOneStore
{

    public function run($date)
    {
        $store = $this->getStore($date);

        if (!$store) {
            return;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $store->status = Stores::STATUS_TERMINATED;
            if ($store->save(false)) {
                $store->terminate();
            }
        } catch (DbException $e) {
            $transaction->rollBack();
            Yii::error($e->getMessage() . $e->getTraceAsString());
            return;
        }
        $transaction->commit();
    }

    /**
     * @param $date
     * @return ActiveRecord|null
     */
    private function getStore($date)
    {
        return Stores::find()
            ->leftJoin('logs', '
            logs.panel_id = stores.id AND
            logs.project_type = :project_type AND
            logs.type = :type AND logs.created_at > :date', [
                ':project_type' => ProjectInterface::PROJECT_TYPE_STORE,
                ':date' => $date,
                ':type' => Logs::TYPE_RESTORED,
            ])
            ->andWhere(['stores.status' => Stores::STATUS_FROZEN])
            ->andWhere(['<', 'stores.expired', $date])
            ->andWhere('logs.id IS NULL')
            ->one();
    }
}
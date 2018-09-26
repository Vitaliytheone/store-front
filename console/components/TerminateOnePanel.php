<?php

namespace console\components;

use common\models\panels\Orders;
use common\models\panels\Project;
use common\models\common\ProjectInterface;
use common\models\panels\Logs;
use yii\db\ActiveRecord;
use yii\db\Exception as DbException;
use Yii;

/**
 * Class TerminateOnePanel
 * @package console\components
 */
class TerminateOnePanel
{

    public function run($date)
    {
        /**
         * @var $order Orders
         */
        foreach (Orders::find()->andWhere('status = :pending AND date < :date', [
            ':pending' => Orders::STATUS_PENDING,
            ':date' => $date // 7 дней; 24 часа; 60 минут; 60 секунд
        ])->all() as $order) {
            $order->cancel();
        }

        $date = strtotime("-1 month", time()); // + 1 месяц

        // Берем по 1 панели на обработку
        $project = $this->getProject($date);

        /**
         * @var Project $project
         */
        if ($project) {
            $transaction = Yii::$app->db->beginTransaction();

            try {
                $project->act = Project::STATUS_TERMINATED;

                if ($project->save(false)) {
                    $project->terminate();
                }
            } catch (DbException $e) {
                $transaction->rollBack();
                Yii::error($e->getMessage() . $e->getTraceAsString());
                return;
            }

            $transaction->commit();
        }
    }

    /**
     * @param $date
     * @return ActiveRecord|null
     */
    private function getProject($date)
    {
        return Project::find()
            ->leftJoin('logs', 'logs.panel_id = project.id AND logs.project_type = :project_type AND logs.type = :type AND logs.created_at > :date', [
                ':project_type' => ProjectInterface::PROJECT_TYPE_PANEL,
                ':date' => $date,
                ':type' => Logs::TYPE_RESTORED
            ])
            ->andWhere([
                'project.act' => Project::STATUS_FROZEN
            ])
            ->andWhere('project.expired < :expired AND logs.id IS NULL', [
                ':expired' => $date
            ])
            ->one();
    }
}
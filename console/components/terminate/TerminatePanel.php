<?php

namespace console\components\terminate;

use common\models\panels\Project;
use common\models\common\ProjectInterface;
use common\models\panels\Logs;
use yii\db\Exception as DbException;
use Yii;

/**
 * Class TerminatePanel
 * @package console\components\terminate
 */
class TerminatePanel
{
    /**
     * @var integer
     */
    protected $_date;

    /**
     * CancelOrder constructor.
     * @param integer $date
     */
    public function __construct($date)
    {
        $this->_date = $date;
    }

    public function run()
    {
        // Берем по 1 панели на обработку
        $project = $this->getProject($this->_date);

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
     * @return Project|null
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
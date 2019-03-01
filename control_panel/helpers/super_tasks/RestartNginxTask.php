<?php

namespace control_panel\helpers\super_tasks;

use Yii;
use yii\base\Exception;
use common\models\sommerces\SuperTasks;
use common\super_tasks\BaseTask;

/**
 * Class RestartNginxTask
 * @package common\super_tasks
 */
class RestartNginxTask extends BaseTask
{
    /**
     * @throws Exception
     */
    public function run(): void
    {
        $tasks = SuperTasks::findAll([
            'task' => SuperTasks::TASK_RESTART_NGINX,
            'status' => SuperTasks::STATUS_PENDING
        ]);

        if (!$tasks) {
            return;
        }

        /** @var SuperTasks $firstTask */
        $firstTask = array_shift($tasks);

        exec(Yii::$app->params['nginx_restart'], $cmdRunOutput, $cmdRunResult);

        // Unsuccess
        if ((int)$cmdRunResult !== 0) {
            $firstTask->status = SuperTasks::STATUS_ERROR;
            $firstTask->save(false);

            throw new Exception('Can not restart Nginx!');
        }

        // Success
        $firstTask->status = SuperTasks::STATUS_COMPLETED;
        $firstTask->save(false);

        // Mark all tasks as successfully completed
        SuperTasks::updateAll([
            'status' => SuperTasks::STATUS_COMPLETED
        ],
            [
                'task' => SuperTasks::TASK_RESTART_NGINX,
                'status' => SuperTasks::STATUS_PENDING
            ]);
    }
}

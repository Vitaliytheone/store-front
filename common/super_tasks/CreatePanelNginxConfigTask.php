<?php
namespace common\super_tasks;

use common\models\panels\Orders;
use common\models\panels\Project;
use Yii;
use yii\base\Exception;
use common\models\panels\SuperTasks;
use yii\helpers\ArrayHelper;

/**
 * Class CreatePanelNginxConfigTask
 * @package common\super_tasks
 */
class CreatePanelNginxConfigTask extends BaseTask {

    public function run(): void
    {
        $tasks = SuperTasks::findAll([
            'task' => SuperTasks::TASK_CREATE_PANEL_NGINX_CONFIG,
            'status' => SuperTasks::STATUS_PENDING,
        ]);
        $tasks = ArrayHelper::index($tasks, 'item_id');

        $projects = Project::find()->andWhere([
            'id' => array_keys($tasks)
        ])->all();

        /**
         * @var $project Project
         * @var SuperTasks $task
         */
        foreach ($projects as $project) {

            $task = $tasks[$project->id];
            $task->refresh();

            if (SuperTasks::STATUS_PENDING !== $task->status) {
                continue;
            }

            $result = $project->createNginxConfig();

            $task->status = $result ? SuperTasks::STATUS_COMPLETED : SuperTasks::STATUS_ERROR;
            $task->save(false);

            if (!$result) {

                $data = $task->getComment();

                // Check if task is `Create order`
                if (isset($data['order_id'])) {

                    $order = Orders::findOne(['id' => $data['order_id']]);

                    $order->status = Orders::STATUS_ERROR;
                    $order->save(false);
                }

                throw new Exception('Can not create Nginx config file for panel domain ' . $project->site);
            }
        }
    }
}
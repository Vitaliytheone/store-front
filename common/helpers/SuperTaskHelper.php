<?php
namespace common\helpers;

use common\models\panels\Orders;
use common\models\panels\Project;
use common\models\stores\Stores;
use Yii;
use common\models\panels\SuperTasks;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

/**
 * Class SuperTaskHelper
 * @package common\helpers
 */
class SuperTaskHelper
{
    /**
     * Set common Nginx tasks
     * @param Project|Stores $object
     * @param array $data
     */
    public static function setTasksNginx($object, $data = [])
    {
        static::setTaskRestartNginx($project->site);
        static::setTaskCreateNginxConfig($project->id, $project->site, $data);
    }

    /**
     * Create restart Nginx task
     * @param string $domain
     */
    public static function setTaskRestartNginx($domain)
    {
        $superTask = new SuperTasks();

        $superTask->task = SuperTasks::TASK_RESTART_NGINX;
        $superTask->status = SuperTasks::STATUS_PENDING;
        $superTask->setComment([
            'domain' => $domain,
        ]);

        $superTask->save(false);
    }

    /**
     * Create nginx config
     * @param $panelId
     * @param $domain
     * @param array $data
     */
    public static function setTaskCreateNginxConfig($panelId, $domain, $data = [])
    {
        $superTask = new SuperTasks();

        $superTask->task = SuperTasks::TASK_CREATE_PANEL_NGINX_CONFIG;
        $superTask->status = SuperTasks::STATUS_PENDING;
        $superTask->item_id = $panelId;

        $superTask->setComment(array_merge([
            'domain' => $domain,
        ], $data));

        $superTask->save(false);
    }

    /**
     * Run common Nginx tasks
     */
    public static function runTasksNginx()
    {
        self::runTasksCreateNginxConfig();
        self::runTasksRestartNginx();
    }

    /**
     * Run restart Nginx task
     * @throws Exception
     */
    public static function runTasksRestartNginx()
    {
        $tasks = SuperTasks::findAll([
            'task' => SuperTasks::TASK_RESTART_NGINX,
            'status' => SuperTasks::STATUS_PENDING
        ]);

        if (!$tasks) {
            return true;
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

    /**
     * Run create Nginx config file task
     * @throws Exception
     */
    public static function runTasksCreateNginxConfig()
    {
        $tasks = SuperTasks::findAll([
            'task' => SuperTasks::TASK_CREATE_PANEL_NGINX_CONFIG,
            'status' => SuperTasks::STATUS_PENDING,
        ]);

        $projectIds = ArrayHelper::getColumn($tasks, 'item_id');
        $projects = Project::find()->andWhere([
            'id' => $projectIds
        ])->all();

        /** @var $project Project */
        foreach ($projects as $project) {

            $task = SuperTasks::findOne([
                'item_id' => $project->id,
                'status' => SuperTasks::STATUS_PENDING,
            ]);

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

                throw new Exception('Can not create Nginx config file for domain ' . $project->site);
            }
        }
    }
}
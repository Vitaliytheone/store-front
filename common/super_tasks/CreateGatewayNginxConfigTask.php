<?php

namespace common\super_tasks;


use common\models\gateways\Sites;
use common\models\panels\SuperTasks;
use common\models\panels\Orders;
use yii\helpers\ArrayHelper;
use yii\base\Exception;

/**
 * Class CreateGatewayNginxConfigTask
 * @package common\super_tasks
 */
class CreateGatewayNginxConfigTask extends BaseTask
{
    /**
     * @throws \Exception
     */
    public function run(): void
    {
        $tasks = SuperTasks::findAll([
            'task' => SuperTasks::TASK_CREATE_GATEWAY_NGINX_CONFIG,
            'status' => SuperTasks::STATUS_PENDING,
        ]);
        $tasks = ArrayHelper::index($tasks, 'item_id');

        $sites = Sites::find()->andWhere([
            'id' => array_keys($tasks)
        ])->all();

        /**
         * @var Sites $site
         * @var SuperTasks $task
         */
        foreach ($sites as $site) {

            $task = $tasks[$site->id];
            $task->refresh();

            if (SuperTasks::STATUS_PENDING !== $task->status) {
                continue;
            }

            $result = $site->createNginxConfig();

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

                throw new Exception('Can not create Nginx config file for gateway domain ' . $site->domain);
            }
        }
    }
}
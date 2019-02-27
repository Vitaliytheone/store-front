<?php

namespace common\super_tasks;

use common\models\panels\Orders;
use common\models\stores\Stores;
use yii\base\Exception;
use common\models\panels\SuperTasks;
use yii\helpers\ArrayHelper;

/**
 * Class CreateStoreNginxConfigTask
 * @package common\super_tasks
 */
class CreateStoreNginxConfigTask extends BaseTask
{
    /**
     * @throws \Exception
     */
    public function run(): void
    {
        $tasks = SuperTasks::findAll([
            'task' => SuperTasks::TASK_CREATE_STORE_NGINX_CONFIG,
            'status' => SuperTasks::STATUS_PENDING,
        ]);
        $tasks = ArrayHelper::index($tasks, 'item_id');

        $stores = Stores::find()->andWhere([
            'id' => array_keys($tasks)
        ])->all();

        /**
         * @var Stores $store
         * @var SuperTasks $task
         */
        foreach ($stores as $store) {

            $task = $tasks[$store->id];
            $task->refresh();

            if (SuperTasks::STATUS_PENDING !== $task->status) {
                continue;
            }

            $isSommerse = $this->options['isSommerce'];
            $result = $store->createNginxConfig($isSommerse);

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

                throw new Exception('Can not create Nginx config file for store domain ' . $store->domain);
            }
        }
    }
}
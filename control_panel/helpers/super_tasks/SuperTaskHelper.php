<?php

namespace control_panel\helpers\super_tasks;

use Yii;
use ReflectionClass;
use common\models\sommerces\Stores;
use common\models\sommerces\SuperTasks;
use yii\base\Exception;
use common\super_tasks\CreateSommerceNginxConfigTask;

/**
 * Class SuperTaskHelper
 * @package common\helpers
 */
class SuperTaskHelper
{
    /**
     * Create task
     * @param integer $item
     * @param integer|null $itemId
     * @param array $data
     */
    public static function setTask($item, $itemId = null, $data = [])
    {
        $superTask = new SuperTasks();

        $superTask->task = $item;
        $superTask->status = SuperTasks::STATUS_PENDING;
        $superTask->item_id = $itemId;

        $superTask->setComment($data);

        $superTask->save(false);
    }

    /**
     * Run common tasks
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     */
    public static function runTasks()
    {
        Yii::$container->get(CreateSommerceNginxConfigTask::class, [])->run();
        Yii::$container->get(RestartNginxTask::class, [])->run();
    }

    /**
     * Set common Nginx tasks
     * @param $object
     * @param array $data
     * @throws Exception
     * @throws \ReflectionException
     */
    public static function setTasksNginx($object, $data = [])
    {
        switch ((new ReflectionClass($object))->getShortName()) {
            case 'Stores':
                /**
                 * @var Stores $object
                 */
                $domain = $object->domain;
                $item = SuperTasks::TASK_CREATE_STORE_NGINX_CONFIG;
                break;

            default:
                throw new Exception();
        }

        static::setTask(SuperTasks::TASK_RESTART_NGINX, null, [
            'domain' => $domain,
        ]);

        static::setTask($item, $object->id, array_merge([
            'domain' => $domain,
        ], $data));
    }
}
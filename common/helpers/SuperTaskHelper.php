<?php
namespace common\helpers;

use common\models\gateways\Sites;
use common\super_tasks\CreateGatewayNginxConfigTask;
use common\super_tasks\CreatePanelNginxConfigTask;
use common\super_tasks\CreateStoreNginxConfigTask;
use common\super_tasks\RestartNginxTask;
use Yii;
use ReflectionClass;
use common\models\panels\Project;
use common\models\stores\Stores;
use common\models\panels\SuperTasks;
use yii\base\Exception;

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
     * @param bool $isSommerce
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     */
    public static function runTasks($isSommerce = false)
    {
        Yii::$container->get(CreatePanelNginxConfigTask::class, [])->run();
        Yii::$container->get(CreateStoreNginxConfigTask::class, ['isSommerce' => $isSommerce])->run();
        Yii::$container->get(CreateGatewayNginxConfigTask::class, [])->run();
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
            case 'Project':
                /**
                 * @var Project $object
                 */
                $domain = $object->site;
                $item = SuperTasks::TASK_CREATE_PANEL_NGINX_CONFIG;
                break;

            case 'Stores':
                /**
                 * @var Stores $object
                 */
                $domain = $object->domain;
                $item = SuperTasks::TASK_CREATE_STORE_NGINX_CONFIG;
                break;

            case 'Sites':
                /**
                 * @var Sites $object
                 */
                $domain = $object->domain;
                $item = SuperTasks::TASK_CREATE_GATEWAY_NGINX_CONFIG;
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
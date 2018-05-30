<?php
namespace common\tasks;

use common\models\panels\BackgroundTasks;
use Yii;
use GearmanClient;
use GearmanTask;
use yii\helpers\Json;

/**
 * Class Client
 * @package app\queue
 */
class Client {

    /**
     * @var GearmanClient
     */
    static $_client;

    /**
     * Get client
     * @return GearmanClient
     */
    public static function getInstance()
    {
        if (null == static::$_client) {
            static::$_client = new GearmanClient();
            static::$_client->addServer(Yii::$app->params['gearmanIp'], Yii::$app->params['gearmanPort']);
        }

        return static::$_client;
    }

    /**
     * Add task to queue
     * @param int $type
     * @param string $code
     * @param mixed $data
     * @return mixed
     */
    public static function addTask($type, $code, $data)
    {
        $client = static::getInstance();

        $unique = md5(microtime() . microtime() . microtime());

        BackgroundTasks::add($type, $code, $unique, $data);

        $jobHandle = $client->doBackground(Yii::$app->params['gearmanPrefix'] . 'worker', Json::encode([
            'code' => $code,
            'data' => $data
        ]), $unique);

        $client->setCompleteCallback(function(GearmanTask $task) {
            BackgroundTasks::setStatus($task->unique(), BackgroundTasks::STATUS_COMPLETED);
        });

        $client->setExceptionCallback(function(GearmanTask $task) {
            BackgroundTasks::setStatus($task->unique(), BackgroundTasks::STATUS_ERROR);
        });

        $client->setFailCallback(function(GearmanTask $task) {
            BackgroundTasks::setStatus($task->unique(), BackgroundTasks::STATUS_ERROR);
        });

        return $jobHandle;
    }
}
<?php
namespace control_panel\tasks;

use Yii;
use GearmanClient;
use yii\helpers\Json;

/**
 * Class Client
 * @package control_panel\tasks
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
     * @param string $code
     * @param mixed $data
     * @return mixed
     */
    public static function addTask($code, $data)
    {
        $client = static::getInstance();

        return $client->doNormal(Yii::$app->params['gearmanPrefix'] . 'worker', Json::encode([
            'code' => $code,
            'data' => $data
        ]));
    }
}
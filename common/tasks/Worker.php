<?php
namespace common\tasks;

use Yii;
use GearmanWorker;
use GearmanJob;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use Exception;

/**
 * Class Worker
 * @package app\queue
 */
class Worker {

    /**
     * @var GearmanWorker
     */
    static $_worker;

    /**
     * Get client
     * @return GearmanWorker
     */
    public static function getInstance()
    {
        if (null == static::$_worker) {
            static::$_worker = new GearmanWorker();
            static::$_worker->addServer(Yii::$app->params['gearmanIp'], Yii::$app->params['gearmanPort']);
            static::$_worker->addFunction(Yii::$app->params['gearmanPrefix'] . 'worker', function(GearmanJob $job) {
                try {
                    $content = $job->workload();
                    $content = Json::decode($content);

                    Listener::run(ArrayHelper::getValue($content, 'code'), ArrayHelper::getValue($content, 'data'));

                    $job->sendData($job->workload());
                } catch (Exception $e) {
                    Yii::error($e->getMessage());
                }
            });
        }

        return static::$_worker;
    }

    /**
     * Start worker
     * @return void
     */
    public static function start()
    {
        if (static::isProcessing()) {
            return;
        }

        static::createPid();

        if (!static::isProcessing()) {
            return;
        }

        while(static::getInstance()->work()){
            if (!static::isProcessing()) {
                break;
            }
        }
    }

    /**
     * Stop worker
     * @return void
     */
    public static function stop()
    {
        if (static::isProcessing()) {
            unlink(static::pid());
        }
    }

    /**
     * Check is processing
     * @return bool
     */
    public static function isProcessing()
    {
        return file_exists(static::pid());
    }

    /**
     * Get path to worker processing file
     * @return string
     */
    private static function pid()
    {
        return Yii::getAlias('@runtime') . '/gearman.pid';
    }

    /**
     * Create pid file
     */
    private static function createPid()
    {
        file_put_contents(static::pid(), time());
    }
}
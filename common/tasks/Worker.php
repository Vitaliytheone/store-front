<?php
namespace common\tasks;

use common\models\panels\BackgroundTasks;
use Yii;
use GearmanWorker;
use GearmanJob;
use yii\base\InvalidCallException;
use yii\helpers\ArrayHelper;
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
                    BackgroundTasks::setStatus($job->unique(), BackgroundTasks::STATUS_IN_PROGRESS);

                    $content = $job->workload();
                    $content = json_decode($content);

                    $result = Listener::run(ArrayHelper::getValue($content, 'code'), ArrayHelper::getValue($content, 'data'), $response);

                    $job->sendData($job->workload());

                    if ($result) {
                        BackgroundTasks::setStatus($job->unique(), BackgroundTasks::STATUS_COMPLETED, $response);
                    } else {
                        BackgroundTasks::setStatus($job->unique(), BackgroundTasks::STATUS_ERROR, $response);
                    }
                } catch (Exception $e) {
                    Yii::error($e->getMessage());
                    BackgroundTasks::setStatus($job->unique(), BackgroundTasks::STATUS_ERROR, $e->getMessage());
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
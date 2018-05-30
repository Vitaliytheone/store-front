<?php
namespace console\controllers;

use common\models\panels\BackgroundTasks;
use common\tasks\Client;
use yii\console\Controller;
use common\tasks\Worker;
use yii\helpers\Console;

/**
 * Class WorkerController
 * @package app\commands
 */
class WorkerController extends Controller
{
    /**
     * Start worker
     */
    public function actionStart()
    {
        Worker::start();
    }

    /**
     * Stop worker
     */
    public function actionStop()
    {
        Worker::stop();
    }

    /**
     * Restart worker
     */
    public function actionRestart()
    {
        Worker::stop();

        Worker::start();
    }

    /**
     * Action test gearman worker
     */
    public function actionTest()
    {
        $result = Client::addTask(BackgroundTasks::TYPE_PANELS, 'test', [
            'time' => time(),
            'message' => 'Hello world'
        ]);

        $this->stderr('Result: ' . $result . "\n", Console::FG_GREEN);
    }
}
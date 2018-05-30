<?php
namespace console\controllers;

use console\components\MainController;
use Yii;
use common\models\panels\BackgroundTasks;
use common\tasks\Client;
use common\tasks\Worker;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;

/**
 * Class WorkerController
 * @package app\commands
 */
class WorkerController extends MainController
{
    public $key;

    public $status;

    public function options($actionID)
    {
        return ['key', 'status'];
    }

    public function init()
    {
        $this->frontendPath = Yii::getAlias('@sommerce/config');

        parent::init(); // TODO: Change the autogenerated stub
    }

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
     * Restart tasks
     */
    public function actionRestartTasks()
    {
        $status = ArrayHelper::getValue($this, 'status', [
            BackgroundTasks::STATUS_PENDING,
            BackgroundTasks::STATUS_ERROR,
            BackgroundTasks::STATUS_IN_PROGRESS
        ]);

        $query = BackgroundTasks::find();

        if (!empty($status)) {
            $query->andWhere([
                'status' => $status
            ]);
        }

        if (!empty($this->key)) {
            $query->andWhere([
                'key' => $this->key
            ]);
        }

        /**
         * @var BackgroundTasks $task
         */
        foreach ($query->batch() as $tasks) {
            foreach ($tasks as $task) {
                Client::addTask($task->type, $task->code, $task->getData(), $task->key);
            }
        }
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
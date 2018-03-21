<?php
namespace my\tasks\workers;

/**
 * Class TestWorker
 * @package my\tasks\workers
 */
class TestWorker extends BaseWorker {

    public static function run($data)
    {
        file_put_contents(\Yii::getAlias('@runtime') . '/run/test_worker_' . time(), "test " . "\r\n" . var_export($data, true) . "\r\n");
    }
}
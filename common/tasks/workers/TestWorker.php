<?php
namespace common\tasks\workers;

/**
 * Class TestWorker
 * @package app\tasks\workers
 */
class TestWorker extends BaseWorker {

    public static function run($data)
    {
        @file_put_contents(\Yii::getAlias('@runtime') . '/test_worker_' . time(), "test " . "\r\n" . var_export($data, true) . "\r\n");
        return true;
    }
}
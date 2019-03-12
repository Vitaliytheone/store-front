<?php
namespace control_panel\tasks\workers;

/**
 * Class TestWorker
 * @package control_panel\tasks\workers
 */
class TestWorker extends BaseWorker {

    public static function run($data)
    {
        file_put_contents(\Yii::getAlias('@runtime') . '/run/test_worker_' . time(), "test " . "\r\n" . var_export($data, true) . "\r\n");
    }
}
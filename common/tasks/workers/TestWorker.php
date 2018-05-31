<?php
namespace common\tasks\workers;

/**
 * Class TestWorker
 * @package app\tasks\workers
 */
class TestWorker extends BaseWorker {

    /**
     * @return bool
     */
    public function run(): bool
    {
        @file_put_contents(\Yii::getAlias('@runtime') . '/test_worker_' . time(), "test " . "\r\n" . var_export($this->_data, true) . "\r\n");

        return true;
    }
}
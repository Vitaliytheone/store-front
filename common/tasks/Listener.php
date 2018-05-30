<?php
namespace common\tasks;

use common\tasks\workers\MailerWorker;
use common\tasks\workers\TestWorker;

/**
 * Class Listener
 * @package app\tasks
 */
class Listener {

    /**
     * Run workers
     * @param string $code
     * @param mixed $data
     * @return null|mixed
     */
    public static function run($code, $data)
    {
        $result = null;
        switch ($code) {
            case 'test':
                $result = TestWorker::run($data);
            break;

            case 'mail':
                $result = MailerWorker::run($data);
            break;
        }

        return $result;
    }
}
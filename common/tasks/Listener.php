<?php
namespace common\tasks;

use common\tasks\workers\MailerWorker;
use common\tasks\workers\TestWorker;

/**
 * Class Listener
 * @package app\tasks
 */
class Listener {

    /*
     * Run workers
     * @return void
     */
    public static function run($code, $data)
    {
        switch ($code) {
            case 'test':
                TestWorker::run($data);
            break;

            case 'mail':
                MailerWorker::run($data);
            break;
        }
    }
}
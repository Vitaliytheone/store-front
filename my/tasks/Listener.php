<?php
namespace my\tasks;

use my\tasks\workers\MailerWorker;
use my\tasks\workers\TestWorker;

/**
 * Class Listener
 * @package my\tasks
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
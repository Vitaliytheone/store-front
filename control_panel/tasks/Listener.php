<?php
namespace control_panel\tasks;

use control_panel\tasks\workers\MailerWorker;
use control_panel\tasks\workers\TestWorker;

/**
 * Class Listener
 * @package control_panel\tasks
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
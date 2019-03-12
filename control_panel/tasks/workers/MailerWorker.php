<?php
namespace control_panel\tasks\workers;

use control_panel\mail\mailers\BaseMailer;

/**
 * Class MailerWorker
 * @package control_panel\tasks\workers
 */
class MailerWorker extends BaseWorker {

    public static function run($data)
    {
        BaseMailer::sendNow($data);
    }
}
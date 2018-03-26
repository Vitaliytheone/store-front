<?php
namespace my\tasks\workers;

use my\mail\mailers\BaseMailer;

/**
 * Class MailerWorker
 * @package my\tasks\workers
 */
class MailerWorker extends BaseWorker {

    public static function run($data)
    {
        BaseMailer::sendNow($data);
    }
}
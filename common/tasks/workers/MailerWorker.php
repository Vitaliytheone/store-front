<?php
namespace common\tasks\workers;

use common\mail\mailers\BaseMailer;

/**
 * Class MailerWorker
 * @package app\tasks\workers
 */
class MailerWorker extends BaseWorker {

    public static function run($data)
    {
        return BaseMailer::sendNow($data);
    }
}
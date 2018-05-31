<?php
namespace common\tasks\workers;

use common\mail\mailers\BaseMailer;

/**
 * Class MailerWorker
 * @package app\tasks\workers
 */
class MailerWorker extends BaseWorker {

    /**
     * @return bool
     */
    public function run(): bool
    {
        return (boolean)BaseMailer::sendNow($this->_data, $this->_error);
    }
}
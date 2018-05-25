<?php
namespace common\mail\mailers\store;

use common\models\stores\Stores;
use yii\helpers\ArrayHelper;
use common\mail\mailers\BaseMailer;

/**
 * Class ContactFormMailer
 * @package common\mail\mailers\store
 */
class ContactFormMailer extends BaseMailer {

    /**
     * Init options
     */
    public function init()
    {
        /**
         * @var Stores $store
         */
        $store = ArrayHelper::getValue($this->options, 'store');
        $clientIp = (string)ArrayHelper::getValue($this->options, 'clientIp');
        $clientBrowser = (string)ArrayHelper::getValue($this->options, 'clientBrowser');
        $name = (string)ArrayHelper::getValue($this->options, 'name');
        $subject = (string)ArrayHelper::getValue($this->options, 'subject');
        $email = (string)ArrayHelper::getValue($this->options, 'email');
        $message = (string)ArrayHelper::getValue($this->options, 'message');

        $this->text =
            "Name: $name" .       PHP_EOL .
            "Subject: $subject" . PHP_EOL .
            "E-mail: $email" .    PHP_EOL . PHP_EOL .

            "Message: $message" . PHP_EOL . PHP_EOL .

            "IP: $clientIp" .           PHP_EOL .
            "Browser: $clientBrowser" . PHP_EOL;

        $this->subject = $subject;
        $this->to = $store->getAdminEmail();
    }
}
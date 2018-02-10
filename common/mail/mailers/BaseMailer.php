<?php
namespace common\mail\mailers;

use common\components\email\Mailgun;
use Yii;
use common\tasks\Client;
use yii\helpers\ArrayHelper;

/**
 * Class BaseMailer
 * @package app\mail\mailers
 */
abstract class BaseMailer {

    public $code;
    public $to;
    public $message;
    public $subject;

    /**
     * @var bool
     */
    public $unique = true;

    /**
     * @var bool - Is send email now or use gearman
     */
    public $now = false;

    /**
     * @var array
     */
    public $notificationOptions = [];

    /**
     * @var array
     */
    public $options = [];

    abstract public function init();

    /**
     * CreatedProject constructor.
     * @param $options
     */
    public function __construct($options)
    {
        $this->options = $options;

        $this->init();
    }

    /**
     * Send
     */
    public function send()
    {
        $options = [
            'to' => $this->to,
            'message' => $this->message,
            'subject' => $this->subject,
        ];

        if ($this->now) {
            return static::sendNow($options);
        } else {
            return Client::addTask('mail', $options);
        }
    }

    /**
     * Send now
     * @param $data
     * @return bool
     */
    public static function sendNow($data)
    {
        $to = ArrayHelper::getValue($data, 'to');
        $subject = ArrayHelper::getValue($data, 'subject');
        $message = ArrayHelper::getValue($data, 'message');

        if (!empty(Yii::$app->params['debugEmail'])) {
            $to = Yii::$app->params['debugEmail'];
        }

        if (!(bool)Mailgun::send($to, $subject, $message)) {
            return false;
        }

        return true;
    }
}
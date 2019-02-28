<?php
namespace control_panel\mail\mailers;

use common\models\panels\NotificationEmail;
use common\models\panels\Notifications;
use Yii;
use control_panel\tasks\Client;
use yii\helpers\ArrayHelper;

/**
 * Class BaseMailer
 * @package control_panel\mail\mailers
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
     * @var NotificationEmail
     */
    public $notificationEmail;

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

        if (isset(Yii::$app->params['mailer.sendNow'])) {
            $this->now = (boolean)Yii::$app->params['mailer.sendNow'];
        }

        $this->notificationEmail = NotificationEmail::findOne([
            'code' => $this->code,
            'enabled' => NotificationEmail::STATUS_ENABLED
        ]);

        if ($this->notificationEmail) {
            $this->init();
        }
    }

    /**
     * Send
     */
    public function send()
    {
        if (!$this->notificationEmail) {
            return false;
        }

        $options = [
            'to' => $this->to,
            'message' => $this->message,
            'subject' => $this->subject,
            'unique' => $this->unique,
            'notificationOptions' => [
                'type' => $this->code,
                'item' => ArrayHelper::getValue($this->notificationOptions, 'item'),
                'item_id' => ArrayHelper::getValue($this->notificationOptions, 'item_id'),
            ]
        ];

        if ($this->now) {
            static::sendNow($options);
        } else {
            Client::addTask('mail', $options);
        }
    }

    /**
     * Send now
     * @param $data
     * @return bool
     */
    public static function sendNow($data)
    {
        $unique = ArrayHelper::getValue($data, 'unique', false);
        $to = ArrayHelper::getValue($data, 'to');
        $subject = ArrayHelper::getValue($data, 'subject');
        $message = ArrayHelper::getValue($data, 'message');
        $notificationOptions = ArrayHelper::getValue($data, 'notificationOptions', []);

        $notificationAttributes = [
            'type' => ArrayHelper::getValue($notificationOptions, 'type', ''),
            'item' => ArrayHelper::getValue($notificationOptions, 'item', ''),
            'item_id' => ArrayHelper::getValue($notificationOptions, 'item_id')
        ];

        if ($unique && Notifications::findOne($notificationAttributes)) {
            return false;
        }

        if (!empty(Yii::$app->params['debugEmail'])) {
            $to = Yii::$app->params['debugEmail'];
        }
        
        $mailer = Yii::$app->mailer->compose()
            ->setFrom(Yii::$app->params['noreplyEmail'])
            ->setTo($to)
            ->setSubject($subject)
            ->setTextBody($message);

        $response = $mailer->toString();

        if (!$mailer->send()) {
            return false;
        }

        $notification = new Notifications();
        $notification->attributes = $notificationAttributes;
        $notification->response = $response;

        $notification->save(false);
    }
}
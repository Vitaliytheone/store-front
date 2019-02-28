<?php
namespace control_panel\mail\mailers;

use common\models\panels\Notifications;
use yii\helpers\ArrayHelper;

/**
 * Class NewMessage
 * @package control_panel\mail\mailers
 */
class NewMessage extends BaseMailer {

    public $code = 'new_message';

    /**
     * Init options
     */
    public function init()
    {
        $message = ArrayHelper::getValue($this->options, 'message');
        $ticket = $message->ticket;

        $this->notificationOptions = [
            'item' => Notifications::ITEM_TICKET,
            'item_id' => $ticket->id
        ];

        $this->message = ArrayHelper::getValue($this->notificationEmail, 'message');
        $this->subject = ArrayHelper::getValue($this->notificationEmail, 'subject');
        $this->to = $ticket->customer->email;
    }
}
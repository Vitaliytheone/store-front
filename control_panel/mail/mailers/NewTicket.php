<?php
namespace control_panel\mail\mailers;

use common\models\panels\Notifications;
use yii\helpers\ArrayHelper;

/**
 * Class NewTicket
 * @package control_panel\mail\mailers
 */
class NewTicket extends BaseMailer {

    public $code = 'new_ticket';

    /**
     * Init options
     */
    public function init()
    {
        $ticket = ArrayHelper::getValue($this->options, 'ticket');

        $this->notificationOptions = [
            'item' => Notifications::ITEM_TICKET,
            'item_id' => $ticket->id
        ];

        $this->message = ArrayHelper::getValue($this->notificationEmail, 'message');
        $this->subject = ArrayHelper::getValue($this->notificationEmail, 'subject');
        $this->to = $ticket->customer->email;
    }
}
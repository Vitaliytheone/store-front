<?php
namespace control_panel\mail\mailers;
use common\models\panels\Notifications;
use yii\helpers\ArrayHelper;

/**
 * Class RenewedSSL
 * @package control_panel\mail\mailers
 */
class RenewedSSL extends BaseMailer {

    public $code = 'ssl_renewed';

    /**
     * Init options
     */
    public function init()
    {
        $ssl = ArrayHelper::getValue($this->options, 'ssl');

        $this->notificationOptions = [
            'item' => Notifications::ITEM_SSL,
            'item_id' => $ssl->id
        ];

        $this->message = ArrayHelper::getValue($this->notificationEmail, 'message');
        $this->subject = ArrayHelper::getValue($this->notificationEmail, 'subject');
        $this->to = $ssl->customer->email;
    }
}
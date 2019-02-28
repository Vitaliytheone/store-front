<?php
namespace control_panel\mail\mailers;
use common\models\panels\Notifications;
use yii\helpers\ArrayHelper;

/**
 * Class CreatedSSL
 * @package control_panel\mail\mailers
 */
class CreatedSSL extends BaseMailer {

    public $code = 'ssl_issued';

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
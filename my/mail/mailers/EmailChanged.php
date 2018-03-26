<?php
namespace my\mail\mailers;
use common\models\panels\Notifications;
use yii\helpers\ArrayHelper;

/**
 * Class EmailChanged
 * @package my\mail\mailers
 */
class EmailChanged extends BaseMailer {

    public $code = 'email_changed';

    public $unique = false;

    /**
     * Init options
     */
    public function init()
    {
        $customer = ArrayHelper::getValue($this->options, 'customer');

        $this->message = ArrayHelper::getValue($this->notificationEmail, 'message');
        $this->subject = ArrayHelper::getValue($this->notificationEmail, 'subject');
        $this->to = $customer->email;

        $this->notificationOptions = [
            'item' => Notifications::ITEM_CUSTOMER,
            'item_id' => $customer->id
        ];
    }
}
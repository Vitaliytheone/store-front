<?php
namespace control_panel\mail\mailers;

use common\models\sommerces\Notifications;
use yii\helpers\ArrayHelper;

/**
 * Class PaypalFailed
 * @package control_panel\mail\mailers
 */
class PaypalFailed extends BaseMailer {

    public $code = 'paypal_failed';

    /**
     * Init options
     */
    public function init()
    {
        $payment = ArrayHelper::getValue($this->options, 'payment');
        $customer = ArrayHelper::getValue($this->options, 'customer');

        $this->notificationOptions = [
            'item' => Notifications::ITEM_PAYMENTS,
            'item_id' => $payment->id
        ];

        $this->message = ArrayHelper::getValue($this->notificationEmail, 'message');
        $this->subject = ArrayHelper::getValue($this->notificationEmail, 'subject');
        $this->to = $customer->email;
    }
}
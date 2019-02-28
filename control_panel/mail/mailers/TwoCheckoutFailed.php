<?php
namespace control_panel\mail\mailers;
use common\models\sommerces\Notifications;
use yii\helpers\ArrayHelper;

/**
 * Class TwoCheckoutFailed
 * @package control_panel\mail\mailers
 */
class TwoCheckoutFailed extends BaseMailer {

    public $code = '2checkout_failed';

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
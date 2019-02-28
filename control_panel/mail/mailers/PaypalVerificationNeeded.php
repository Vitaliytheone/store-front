<?php
namespace control_panel\mail\mailers;

use common\models\sommerces\Notifications;
use common\models\sommerces\Payments;
use yii\helpers\ArrayHelper;
use control_panel\helpers\Url;

/**
 * Class PaypalReviewed
 * @package control_panel\mail\mailers
 */
class PaypalVerificationNeeded extends BaseMailer {

    public $code = 'paypal_verify';

    public $now = false;

    public $unique = false;

    /**
     * Init options
     */
    public function init()
    {
        /** @var Payments $payment */
        $payment = ArrayHelper::getValue($this->options, 'payment');
        $email = ArrayHelper::getValue($this->options, 'email');

        $this->notificationOptions = [
            'item' => Notifications::ITEM_PAYMENTS,
            'item_id' => $payment->id
        ];

        $this->to = $email;
        $this->message = ArrayHelper::getValue($this->notificationEmail, 'message');
        $this->subject = ArrayHelper::getValue($this->notificationEmail, 'subject');

        $verificationUrl = Url::toRoute('/paypal-verify/' . $payment->verification_code, 'https');

        $this->message = str_replace([
            '{{verify_link}}'
        ], [
            $verificationUrl
        ], $this->message);
    }
}
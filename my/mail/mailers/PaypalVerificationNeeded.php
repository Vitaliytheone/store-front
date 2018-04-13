<?php
namespace my\mail\mailers;

use common\models\panels\Notifications;
use common\models\panels\Payments;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/**
 * Class PaypalReviewed
 * @package my\mail\mailers
 */
class PaypalVerificationNeeded extends BaseMailer {

    public $code = 'paypal_verify';

    public $now = true; // TODO:: Remove it to default for production

    public $unique = false;

    /**
     * Init options
     */
    public function init()
    {
        /** @var Payments $payment */
        $payment = ArrayHelper::getValue($this->options, 'payment');
        $email = ArrayHelper::getValue($this->options, 'email');

        error_log($email);

        $__email = 'alex.fatyeev@yandex.ru';

        $this->notificationOptions = [
            'item' => Notifications::ITEM_PAYMENTS,
            'item_id' => $payment->id
        ];

        $this->message = ArrayHelper::getValue($this->notificationEmail, 'message');
        $this->subject = ArrayHelper::getValue($this->notificationEmail, 'subject');
        $this->to = $__email;

        $verificationUrl = Url::toRoute('/payer-verify/' . $payment->verification_code, true);

        $this->message = str_replace([
            '{{verify_link}}'
        ], [
            $verificationUrl
        ], $this->message);

        $this->message = $this->message . '|||' . $email;
    }
}
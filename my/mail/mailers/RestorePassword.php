<?php
namespace my\mail\mailers;
use common\models\panels\Notifications;
use yii\helpers\ArrayHelper;
use my\helpers\Url;

/**
 * Class RestorePassword
 * @package my\mail\mailers
 */
class RestorePassword extends BaseMailer {

    public $code = 'restore_password';

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

        $url = Url::toRoute('/reset/' . $customer->token, true);

        $this->message = str_replace([
            '{{restore_url}}'
        ], [
            $url
        ], $this->message);
    }
}
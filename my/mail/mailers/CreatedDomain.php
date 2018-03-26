<?php
namespace my\mail\mailers;
use common\models\panels\Notifications;
use yii\helpers\ArrayHelper;

/**
 * Class CreatedDomain
 * @package my\mail\mailers
 */
class CreatedDomain extends BaseMailer {

    public $code = 'domain_issued';

    /**
     * Init options
     */
    public function init()
    {
        $domain = ArrayHelper::getValue($this->options, 'domain');

        $this->notificationOptions = [
            'item' => Notifications::ITEM_DOMAIN,
            'item_id' => $domain->id
        ];

        $this->message = ArrayHelper::getValue($this->notificationEmail, 'message');
        $this->subject = ArrayHelper::getValue($this->notificationEmail, 'subject');
        $this->to = $domain->customer->email;
    }
}
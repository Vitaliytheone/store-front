<?php

namespace control_panel\mail\mailers;

use common\models\sommerces\Notifications;
use yii\helpers\ArrayHelper;

/**
 * Class InvoiceCreated
 * @package control_panel\mail\mailers
 */
class InvoiceCreated extends BaseMailer {

    public $code = 'invoice_created';

    /**
     * Init options
     */
    public function init()
    {
        $domain = ArrayHelper::getValue($this->options, 'domain');
        $ssl = ArrayHelper::getValue($this->options, 'ssl');
        $store = ArrayHelper::getValue($this->options, 'store');

        if ($domain) {
            $this->to = $domain->customer->email;
            $this->notificationOptions = [
                'item' => Notifications::ITEM_DOMAIN,
                'item_id' => $domain->id
            ];
        } else if ($ssl) {
            $this->to = $ssl->customer->email;
            $this->notificationOptions = [
                'item' => Notifications::ITEM_SSL,
                'item_id' => $ssl->id
            ];
        } else if ($store) {
            $this->to = $store->customer->email;
            $this->notificationOptions = [
                'item' => Notifications::ITEM_STORE,
                'item_id' => $store->id
            ];
        } else {
            $this->notificationEmail = false;
            return false;
        }

        $this->message = ArrayHelper::getValue($this->notificationEmail, 'message');
        $this->subject = ArrayHelper::getValue($this->notificationEmail, 'subject');
    }
}
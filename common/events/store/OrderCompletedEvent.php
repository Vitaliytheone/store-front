<?php
namespace common\events\store;

use common\mail\mailers\store\OrderMailer;
use common\models\store\NotificationTemplates;
use common\models\stores\NotificationDefaultTemplates;
use Yii;

/**
 * Class OrderCompletedEvent
 * @package common\events\store
 */
class OrderCompletedEvent extends BaseOrderEvent {

    /**
     * Run method
     * @return void
     */
    public function run():void
    {
        if (empty($this->_order)) {
            Yii::error('Empty ' . static::class . ' order parameter');
            return;
        }

        $this->_suborders = $this->_order->suborders;
        $this->_payment = $this->_order->payment;

        $this->customerNotify();
    }

    /**
     * Send notification to customer
     */
    protected function customerNotify()
    {
        $template = static::getCrossNotificationByCode(NotificationDefaultTemplates::CODE_ORDER_COMPLETED);

        if (!$template || NotificationTemplates::STATUS_ENABLED !== $template->status) {
            return;
        }

        $mailer = new OrderMailer([
            'to' => $this->_order->customer,
            'order' => $this->_order,
            'suborders' => $this->_suborders,
            'payment' => $this->_payment,
            'template' => $template,
            'store' => $this->_store,
        ]);
        $mailer->send();
    }
}
<?php
namespace common\events\store;

use common\mail\mailers\store\OrderMailer;
use common\models\store\Checkouts;
use common\models\store\NotificationTemplates;
use common\models\stores\NotificationDefaultTemplates;
use common\models\stores\Stores;
use Yii;

/**
 * Class OrderAbandonedEvent
 * @package common\events\store
 */
class OrderAbandonedEvent extends BaseOrderEvent {

    /**
     * @var Checkouts
     */
    protected $_checkout;

    /**
     * @var Stores
     */
    protected $_store;

    /**
     * OrderAbandonedEvent constructor.
     * @param Checkouts $checkout
     * @param Stores $store
     */
    public function __construct(Checkouts $checkout, Stores $store)
    {
        $this->_checkout = $checkout;
        $this->_store = $store;
    }

    /**
     * Run method
     * @return void
     */
    public function run():void
    {
        if (empty($this->_checkout)) {
            Yii::error('Empty ' . static::class . ' checkout parameter');
            return;
        }

        if (empty($this->_store)) {
            Yii::error('Empty ' . static::class . ' checkout parameter');
            return;
        }

        $this->_suborders = $this->_checkout->suborders;
        $this->_payment = $this->_checkout->payment;

        $this->customerNotify();
    }

    /**
     * Send notification to customer
     */
    protected function customerNotify()
    {
        $template = static::getCrossNotificationByCode(NotificationDefaultTemplates::CODE_ORDER_ABANDONED_CHECKOUT);

        if (!$template || NotificationTemplates::STATUS_ENABLED !== $template->status) {
            return;
        }

        $mailer = new OrderMailer([
            'to' => $this->_checkout->customer,
            'order' => $this->_checkout,
            'suborders' => $this->_suborders,
            'payment' => $this->_payment,
            'template' => $template,
            'store' => $this->_store,
        ]);
        $mailer->send();
    }
}
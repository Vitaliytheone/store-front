<?php
namespace sommerce\events\handlers;

use common\mail\mailers\store\OrderAdminMailer;
use common\mail\mailers\store\OrderWithItemsMailer;
use common\models\sommerce\NotificationAdminEmails;
use common\models\sommerce\Orders;
use common\models\sommerce\NotificationTemplates;
use common\models\sommerce\Suborders;
use common\models\sommerces\NotificationDefaultTemplates;
use common\models\sommerces\Stores;
use Yii;

/**
 * Class OrderCreatedEvent
 * @package sommerce\events\handlers
 */
class OrderCreatedEvent extends BaseOrderEvent {

    /**
     * OrderConfirmEvent constructor.
     * @param Stores $store
     * @param Orders $order
     */
    public function __construct(Stores $store, Orders $order)
    {
        $this->_order = $order;
        $this->_store = $store;
    }

    /**
     * Run method
     * @return void
     */
    public function run():void
    {
        if (empty($this->_order)) {
            Yii::info('Empty ' . static::class . ' order parameter');
            return;
        }

        $this->_suborders = $this->_order->suborders;
        $this->_payment = $this->_order->payment;

        $this->customerNotify();
        $this->adminNotify();
    }

    /**
     * Send notification to customer
     */
    protected function customerNotify()
    {
        $template = static::getCrossNotificationByCode(NotificationDefaultTemplates::CODE_ORDER_CONFIRMATION);

        if (!$template || NotificationTemplates::STATUS_ENABLED !== $template->status) {
            return;
        }

        $mailer = new OrderWithItemsMailer([
            'to' => $this->_order->customer,
            'order' => $this->_order,
            'suborders' => $this->_suborders,
            'payment' => $this->_payment,
            'template' => $template,
            'store' => $this->_store,
        ]);
        $mailer->send();
    }

    /**
     * Send notification to store admins
     */
    protected function adminNotify()
    {
        // Берем активных админов
        $adminEmails = $this->getAdmins();

        if (empty($adminEmails)) {
            return;
        }

        $codes = [];

        foreach ($this->_suborders as $suborder) {
            if (Suborders::MODE_MANUAL == $suborder->mode) {
                $codes[NotificationDefaultTemplates::CODE_ORDER_NEW_MANUAL] = NotificationDefaultTemplates::CODE_ORDER_NEW_MANUAL;
            }

            if (Suborders::MODE_AUTO == $suborder->mode) {
                $codes[NotificationDefaultTemplates::CODE_ORDER_NEW_AUTO] = NotificationDefaultTemplates::CODE_ORDER_NEW_AUTO;
            }
        }

        /**
         * @var NotificationAdminEmails $adminEmail
         */
        foreach ($codes as $code) {
            $template = static::getCrossNotificationByCode($code);

            if (!$template || NotificationTemplates::STATUS_ENABLED !== $template->status) {
                continue;
            }

            // Берем активные шаблоны для оповещения
            foreach ($adminEmails as $adminEmail) {
                $mailer = new OrderAdminMailer([
                    'to' => $adminEmail->email,
                    'order' => $this->_order,
                    'template' => $template,
                    'store' => $this->_store,
                ]);
                $mailer->send();
            }
        }
    }
}
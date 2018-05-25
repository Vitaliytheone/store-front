<?php
namespace common\events\store;

use common\mail\mailers\store\OrderAdminMailer;
use common\models\store\NotificationTemplates;
use common\models\stores\NotificationDefaultTemplates;
use Yii;

/**
 * Class OrderFailEvent
 * @package common\events\store
 */
class OrderFailEvent extends BaseOrderEvent {

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

        $this->adminNotify();
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

        $template = static::getCrossNotificationByCode(NotificationDefaultTemplates::CODE_ORDER_FAIL);

        if (!$template || NotificationTemplates::STATUS_ENABLED !== $template->status) {
            return;
        }

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
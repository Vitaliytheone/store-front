<?php
namespace common\events\store;

use common\mail\mailers\store\OrderAdminMailer;
use common\models\store\Suborders;
use common\models\stores\NotificationDefaultTemplates;
use common\models\stores\Stores;
use Yii;

/**
 * Class OrderErrorEvent
 * @package common\events\store
 */
class OrderErrorEvent extends BaseOrderEvent {

    /**
     * OrderErrorEvent constructor.
     * @param integer $storeId
     * @param integer $suborderId
     */
    public function __construct($storeId, $suborderId)
    {
        if (!static::getTemplate(NotificationDefaultTemplates::CODE_ORDER_ERROR)) {
            return;
        }

        $this->_store = Stores::findOne($storeId);

        if (empty($this->_store)) {
            Yii::error('Empty ' . static::class . ' store parameter');
            return;
        }

        Yii::$app->store->setInstance($this->_store);

        $suborder = Suborders::findOne($suborderId);

        if (empty($suborder)) {
            Yii::error('Empty ' . static::class . ' suborder parameter');
            return;
        }

        $this->_order = $suborder->order;

    }

    /**
     * Run method
     * @return void
     */
    public function run():void
    {
        $this->adminNotify();
    }

    /**
     * Send notification to store admins
     */
    protected function adminNotify()
    {
        if (!($template = static::getTemplate(NotificationDefaultTemplates::CODE_ORDER_ERROR))) {
            return;
        }

        if (empty($this->_order)) {
            Yii::error('Empty ' . static::class . ' order parameter');
            return;
        }

        // Берем активных админов
        $adminEmails = $this->getAdmins();

        if (empty($adminEmails)) {
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
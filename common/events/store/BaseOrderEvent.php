<?php
namespace common\events\store;

use common\models\store\NotificationAdminEmails;
use common\models\store\NotificationTemplates;
use common\models\store\Orders;
use common\models\store\Payments;
use common\models\store\Suborders;
use common\models\stores\NotificationDefaultTemplates;
use common\models\stores\Stores;
use Yii;

/**
 * Class BaseOrderEvent
 * @package common\events\store
 */
abstract class BaseOrderEvent {

    /**
     * @var Stores
     */
    protected $_store;

    /**
     * @var Orders
     */
    protected $_order;

    /**
     * @var Suborders[]
     */
    protected $_suborders;

    /**
     * @var NotificationAdminEmails[]
     */
    protected $_admins;

    /**
     * @var Payments
     */
    protected $_payment;

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
     * @return array|NotificationAdminEmails[]
     */
    protected function getAdmins()
    {
        if (null !== $this->_admins) {
            return $this->_admins;
        }

        $this->_admins = NotificationAdminEmails::find()
            ->active()
            ->all();

        return $this->_admins;
    }

    /**
     * @param $code
     * @return NotificationTemplates|null
     */
    public static function getCrossNotificationByCode($code)
    {
        $notification = NotificationTemplates::getNotificationByCode($code);

        if (!$notification || !$notification->body) {
            $defaultTemplate = NotificationDefaultTemplates::getNotificationByCode($code);

            if ($defaultTemplate) {
                $notification = new NotificationTemplates([
                    'notification_code' => $defaultTemplate->code,
                    'body' => $defaultTemplate->body,
                    'subject' => $defaultTemplate->subject,
                    'status' => $defaultTemplate->status,
                ]);
            }
        }

        return $notification;
    }

    /**
     * Run method
     * @return void
     */
    abstract public function run():void;
}
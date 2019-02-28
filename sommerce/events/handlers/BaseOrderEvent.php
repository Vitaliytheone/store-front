<?php
namespace sommerce\events\handlers;

use common\models\sommerce\NotificationAdminEmails;
use common\models\sommerce\NotificationTemplates;
use common\models\sommerce\Orders;
use common\models\sommerce\Payments;
use common\models\sommerce\Suborders;
use common\models\sommerces\NotificationDefaultTemplates;
use common\models\sommerces\Stores;
use Yii;

/**
 * Class BaseOrderEvent
 * @package sommerce\events\handlers
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
     * @param string $code
     * @return NotificationTemplates
     */
    protected static function getTemplate($code)
    {
        $template = static::getCrossNotificationByCode($code);

        if (!$template || NotificationTemplates::STATUS_ENABLED !== $template->status) {
            return null;
        }

        return $template;
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
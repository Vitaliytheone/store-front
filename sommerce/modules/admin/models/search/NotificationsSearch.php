<?php
namespace sommerce\modules\admin\models\search;

use common\models\sommerce\NotificationAdminEmails;
use common\models\sommerce\NotificationTemplates;
use common\models\sommerces\NotificationDefaultTemplates;
use common\models\sommerces\Stores;
use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * Class NotificationsSearch
 * @package app\models\search
 */
class NotificationsSearch
{
    /**
     * @var array
     */
    protected static $_notifications;

    /**
     * @var array
     */
    protected static $_emails;

    /**
     * @var Stores
     */
    private $_store;

    /**
     * Set store
     * @param Stores $store
     */
    public function setStore($store)
    {
        $this->_store = $store;
    }

    /**
     * Get notifications
     * @return array
     */
    public function getNotifications()
    {
        if (null !== static::$_notifications) {
            return static::$_notifications;
        }

        $db = $this->_store->db_name;

        static::$_notifications = [
            NotificationDefaultTemplates::RECIPIENT_ADMIN => [],
            NotificationDefaultTemplates::RECIPIENT_CUSTOMER => [],
        ];

        $defaultTemplates = (new Query())
            ->select([
                'code',
                'status',
                'recipient'
            ])
            ->from(NotificationDefaultTemplates::tableName())
            ->orderBy([
                'position' => SORT_ASC
            ])
            ->all();
        $defaultTemplates = ArrayHelper::index($defaultTemplates, 'code');

        $storeTemplates = (new Query())
            ->select([
                'notification_code',
                'status',
            ])
            ->from($db . '.' . NotificationTemplates::tableName())
            ->all();
        $storeTemplates = ArrayHelper::index($storeTemplates, 'notification_code');

        foreach ($defaultTemplates as $code => $template) {
            static::$_notifications[$template['recipient']][$code] = [
                'code' => $code,
                'status' => isset($storeTemplates[$code]) ? $storeTemplates[$code]['status'] : $template['status'],
                'label' => Yii::t('admin', 'notifications.label.' . $code),
                'description' => Yii::t('admin', 'notifications.description.' . $code),
            ];
        }

        return static::$_notifications;
    }

    /**
     * Get emails
     * @return array
     */
    public function getEmails()
    {
        if (null !== static::$_emails) {
            return static::$_emails;
        }

        $db = $this->_store->db_name;

        static::$_emails = (new Query())
            ->select([
                'id',
                'email',
                'status',
                'primary'
            ])
            ->from($db . '.' . NotificationAdminEmails::tableName())
            ->all();

        return static::$_emails;
    }
}
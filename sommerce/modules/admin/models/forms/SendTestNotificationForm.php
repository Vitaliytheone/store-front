<?php
namespace sommerce\modules\admin\models\forms;

use common\models\store\NotificationAdminEmails;
use common\models\store\NotificationTemplates;
use common\models\stores\Stores;
use Yii;
use yii\base\Model;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * Class SendTestNotificationForm
 * @package app\modules\superadmin\models\forms
 */
class SendTestNotificationForm extends Model {

    /**
     * @var integer
     */
    public $admin_email_id;

    /**
     * @var NotificationTemplates
     */
    private $_notification;

    /**
     * @var Stores
     */
    private $_store;

    /**
     * @var array
     */
    protected static $adminEmails;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['admin_email_id'], 'integer'],
        ];
    }

    /**
     * Set notification
     * @param NotificationTemplates $notification
     */
    public function setNotification($notification)
    {
        $this->_notification = $notification;
    }

    /**
     * Set store
     * @param Stores $store
     */
    public function setStore($store)
    {
        $this->_store = $store;
    }

    /**
     * Send test notification email
     * @return bool
     */
    public function send()
    {
        if (!$this->validate()) {
            return false;
        }

        $notificationAdmin = NotificationAdminEmails::findOne($this->admin_email_id);

        $testMail = new TestNotificationForm();
        $testMail->setNotification($this->_notification);
        $testMail->setStore($this->_store);
        $testMail->setEmail($notificationAdmin->email);

        return $testMail->send();
    }

    /**
     * Get admin emails
     * @return array
     */
    public function getAdminEmails()
    {
        if (null !== static::$adminEmails) {
            return static::$adminEmails;
        }

        /**
         * @var Stores $store
         */
        $store = Yii::$app->store->getInstance();

        static::$adminEmails = [];

        $adminEmails = (new Query())
            ->select([
                'id',
                'email'
            ])
            ->from($store->db_name . '.' . NotificationAdminEmails::tableName())
            ->andWhere([
                'status' => NotificationAdminEmails::STATUS_ENABLED
            ])
            ->all();

        static::$adminEmails = ArrayHelper::map($adminEmails, 'id', 'email');

        return static::$adminEmails;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'admin_email_id' => Yii::t('app', 'Send to'),
        ];
    }
}
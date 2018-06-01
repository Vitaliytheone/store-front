<?php

namespace common\models\stores;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use common\models\stores\queries\NotificationDefaultTemplatesQuery;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%notification_default_templates}}".
 *
 * @property int $id
 * @property string $code
 * @property string $subject
 * @property string $body
 * @property int $status 0 - disabled, 1 - enabled
 * @property int $position
 * @property int $recipient 1 - admin, 2 - customer
 * @property int $created_at
 * @property int $updated_at
 */
class NotificationDefaultTemplates extends ActiveRecord
{
    const STATUS_DISABLED = 0;
    const STATUS_ENABLED = 1;

    const RECIPIENT_ADMIN = 1;
    const RECIPIENT_CUSTOMER = 2;

    const CODE_ORDER_CONFIRMATION  = 'order_confirmation';
    const CODE_ORDER_IN_PROGRESS  = 'order_in_progress';
    const CODE_ORDER_COMPLETED  = 'order_completed';
    const CODE_ORDER_ABANDONED_CHECKOUT  = 'abandoned_checkout';
    const CODE_ORDER_NEW_AUTO  = 'new_auto_order';
    const CODE_ORDER_NEW_MANUAL  = 'new_manual_order';
    const CODE_ORDER_FAIL  = 'order_fail';
    const CODE_ORDER_ERROR  = 'order_error';

    /**
     * @var static[]
     */
    public static $notifications;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return DB_STORES . '.notification_default_templates';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['code', 'position'], 'required'],
            [['body'], 'string'],
            [['position', 'created_at', 'updated_at', 'status', 'recipient'], 'integer'],
            [['code', 'subject'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'code' => Yii::t('app', 'Code'),
            'subject' => Yii::t('app', 'Subject'),
            'body' => Yii::t('app', 'Body'),
            'status' => Yii::t('app', 'Status'),
            'position' => Yii::t('app', 'Position'),
            'recipient' => Yii::t('app', 'Recipient'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @inheritdoc
     * @return NotificationDefaultTemplatesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new NotificationDefaultTemplatesQuery(get_called_class());
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => [
                        'created_at',
                        'updated_at'
                    ],
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'created_at',
                ],
                'value' => function() {
                    return time();
                },
            ],
        ];
    }

    /**
     * @return array
     */
    public static function getRecipients()
    {
        return [
            static::RECIPIENT_ADMIN => Yii::t('admin', 'notification.recipient.admin'),
            static::RECIPIENT_CUSTOMER => Yii::t('admin', 'notification.recipient.customer'),
        ];
    }

    /**
     * @return array
     */
    public static function getStatuses()
    {
        return [
            static::STATUS_DISABLED => Yii::t('admin', 'notification.status.disabled'),
            static::STATUS_ENABLED => Yii::t('admin', 'notification.status.enabled'),
        ];
    }

    /**
     * Get status string name by status
     * @param int $status
     * @return mixed
     */
    public static function getStatusNameString($status)
    {
        return ArrayHelper::getValue(static::getStatuses(), $status, '');
    }

    /**
     * Get recipient string name by recipient code
     * @param int $recipient
     * @return mixed
     */
    public static function getRecipientNameString($recipient)
    {
        return ArrayHelper::getValue(static::getRecipients(), $recipient, '');
    }

    /**
     * Get status name
     * @return mixed
     */
    public function getStatusName()
    {
        return static::getStatusNameString($this->status);
    }

    /**
     * Get recipient name
     * @return mixed
     */
    public function getRecipientName()
    {
        return static::getRecipientNameString($this->recipient);
    }

    /**
     * Get notifications
     * @return null|static[]
     */
    public static function getNotifications()
    {
        if (null !== static::$notifications) {
            return static::$notifications;
        }

        static::$notifications = ArrayHelper::index(static::find()->all(),  'code');

        return static::$notifications;
    }

    /**
     * Get notification by code
     * @param string $code
     * @return null|static
     */
    public static function getNotificationByCode($code)
    {
        return ArrayHelper::getValue(static::getNotifications(), $code);
    }
}
<?php

namespace common\models\store;

use common\models\stores\NotificationDefaultTemplates;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use common\models\store\queries\NotificationTemplatesQuery;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%notification_templates}}".
 *
 * @property int $id
 * @property string $notification_code
 * @property int $status 0 - disabled, 1 - enabled
 * @property string $subject
 * @property string $body
 * @property int $created_at
 * @property int $updated_at
 */
class NotificationTemplates extends ActiveRecord
{
    const STATUS_DISABLED = 0;
    const STATUS_ENABLED = 1;

    /**
     * @var static[]
     */
    public static $notifications;

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->storeDb;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%notification_templates}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['notification_code'], 'required'],
            [['body'], 'string'],
            [['created_at', 'updated_at', 'status'], 'integer'],
            [['notification_code', 'subject'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'notification_code' => Yii::t('app', 'Notification Code'),
            'status' => Yii::t('app', 'Status'),
            'subject' => Yii::t('app', 'Subject'),
            'body' => Yii::t('app', 'Body'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
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
     * @inheritdoc
     * @return NotificationTemplatesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new NotificationTemplatesQuery(get_called_class());
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
     * Get status name
     * @return mixed
     */
    public function getStatusName()
    {
        return static::getStatusNameString($this->status);
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

        static::$notifications = ArrayHelper::index(static::find()->all(),  'notification_code');

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
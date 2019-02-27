<?php

namespace common\models\sommerce;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use common\models\sommerce\queries\NotificationAdminEmailsQuery;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%notification_admin_emails}}".
 *
 * @property int $id
 * @property string $email
 * @property int $status 0 - disabled, 1 - enabled
 * @property int $primary
 * @property int $created_at
 * @property int $updated_at
 */
class NotificationAdminEmails extends ActiveRecord
{
    const STATUS_DISABLED = 0;
    const STATUS_ENABLED = 1;

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
        return '{{%notification_admin_emails}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email'], 'required'],
            [['created_at', 'updated_at', 'status', 'primary'], 'integer'],
            [['email'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'email' => Yii::t('app', 'Email'),
            'status' => Yii::t('app', 'Status'),
            'primary' => Yii::t('app', 'Primary'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @inheritdoc
     * @return NotificationAdminEmailsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new NotificationAdminEmailsQuery(get_called_class());
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
}
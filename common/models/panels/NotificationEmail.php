<?php

namespace common\models\panels;

use Yii;
use common\models\panels\queries\NotificationEmailQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%notification_email}}".
 *
 * @property integer $id
 * @property string $subject
 * @property string $message
 * @property string $code
 * @property integer $enabled
 */
class NotificationEmail extends ActiveRecord
{
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%notification_email}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['subject', 'message', 'code'], 'required'],
            [['subject', 'message'], 'string'],
            [['enabled'], 'integer'],
            [['code'], 'string', 'max' => 300],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'subject' => Yii::t('app', 'Subject'),
            'message' => Yii::t('app', 'Message'),
            'code' => Yii::t('app', 'Code'),
            'enabled' => Yii::t('app', 'Enabled'),
        ];
    }

    /**
     * @inheritdoc
     * @return NotificationEmailQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new NotificationEmailQuery(get_called_class());
    }

    /**
     * Get statuses
     * @return array
     */
    public static function getStatuses()
    {
        return [
            static::STATUS_DISABLED => Yii::t('app', 'notification_email.status.disabled'),
            static::STATUS_ENABLED => Yii::t('app', 'notification_email.status.enabled')
        ];
    }

    /**
     * Get enabled name
     * @return string
     */
    public function getStatusName()
    {
        return static::getStatuses()[$this->enabled];
    }

    /**
     * Toggle enabled/disabled email status
     * @param $status
     * @return bool
     */
    public function changeStatus($status)
    {
        if (static::STATUS_ENABLED == $status) {
            $this->enabled = static::STATUS_ENABLED;
        } else {
            $this->enabled = static::STATUS_DISABLED;
        }

        return $this->save(false);
    }
}

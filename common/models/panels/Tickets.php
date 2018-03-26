<?php

namespace common\models\panels;

use my\components\behaviors\IpBehavior;
use Yii;
use common\components\traits\UnixTimeFormatTrait;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "tickets".
 *
 * @property integer $id
 * @property integer $cid
 * @property integer $pid
 * @property string $subject
 * @property integer $admin
 * @property integer $user
 * @property integer $status
 * @property integer $date
 * @property integer $date_update
 * @property integer $ip
 *
 * @property Customers $customer
 * @property TicketMessages[] $messages
 */
class Tickets extends ActiveRecord
{
    const STATUS_PENDING = 0;
    const STATUS_RESPONDED = 1;
    const STATUS_CLOSED = 4;
    const STATUS_IN_PROGRESS = 3;
    const STATUS_SOLVED = 2;

    use UnixTimeFormatTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tickets';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cid', 'subject'], 'required'],
            [['cid', 'pid', 'admin', 'user', 'status', 'date', 'date_update'], 'integer'],
            [['subject'], 'string', 'max' => 300],
            ['ip', 'string']
        ];
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['date', 'date_update'],
                ],
                'value' => function() {
                    return time();
                },
            ],
            'ip' => [
                'class' => IpBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'ip',
                ]
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'cid' => 'Cid',
            'pid' => 'Pid',
            'subject' => 'Subject',
            'admin' => 'Admin',
            'user' => 'User',
            'status' => 'Status',
            'date' => 'Date',
            'date_update' => 'Date Update',
            'ip' => 'Ip',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMessages()
    {
        return $this->hasMany(TicketMessages::className(), ['tid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(Customers::className(), ['id' => 'cid']);
    }

    /**
     * @return array
     */
    public static function getStatuses()
    {
        return [
            static::STATUS_PENDING => Yii::t('app', 'tickets.status.pending'),
            static::STATUS_RESPONDED => Yii::t('app', 'tickets.status.responded'),
            static::STATUS_CLOSED => Yii::t('app', 'tickets.status.closed'),
            static::STATUS_IN_PROGRESS => Yii::t('app', 'tickets.status.in_progress'),
            static::STATUS_SOLVED => Yii::t('app', 'tickets.status.solved')
        ];
    }

    /**
     * Get status name
     * @return string
     */
    public function getStatusName()
    {
        return static::getStatuses()[$this->status];
    }

    /**
     * Make unread for user ticket
     * @return bool|int
     */
    public function makeUnreaded()
    {
        $this->admin = 1;
        return $this->save(false);
    }

    /**
     * Make readed for user ticket
     * @return bool
     */
    public function makeReaded()
    {
        $this->admin = 0;
        return $this->save(false);
    }

    /**
     * Can create new ticket or not
     * @param int $customerId
     * @return bool
     */
    public static function canCreate($customerId)
    {
        return Yii::$app->params['pending_tickets'] > static::find()->andWhere([
                'cid' => $customerId,
                'status' => static::STATUS_PENDING
            ])->count();
    }
}
<?php

namespace common\models\panels;

use my\components\behaviors\IpBehavior;
use Yii;
use common\components\traits\UnixTimeFormatTrait;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use my\components\behaviors\UserAgentBehavior;

/**
 * This is the model class for table "tickets".
 *
 * @property integer $id
 * @property integer $customer_id
 * @property string $subject
 * @property integer $is_admin
 * @property integer $is_user
 * @property integer $status
 * @property integer $assigned_admin_id
 * @property string $user_agent
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $ip
 * @property Customers $customer
 * @property SuperAdmin $assigned
 * @property TicketMessages[] $messages
 */
class Tickets extends ActiveRecord
{
    public $assigned_name;

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
        return DB_PANELS . '.tickets';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['customer_id', 'subject'], 'required'],
            [['customer_id', 'is_admin', 'is_user', 'status', 'created_at', 'updated_at', 'assigned_admin_id'], 'integer'],
            [['subject'], 'string', 'max' => 300],
            ['ip', 'string'],
            [['user_agent'], 'string', 'max' => 300],
            ['status', 'in', 'range' => [
                self::STATUS_PENDING,
                self::STATUS_RESPONDED,
                self::STATUS_CLOSED,
                self::STATUS_IN_PROGRESS,
                self::STATUS_SOLVED
            ]],
        ];
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                ],
                'value' => function() {
                    return time();
                },
            ],
            'ip' => [
                'class' => IpBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'ip',
                ]
            ],
            'user_agent' => [
                'class' => UserAgentBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'user_agent'
                ]
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' =>  Yii::t('app', 'ID'),
            'customer_id' => Yii::t('app', 'Customer id'),
            'subject' => Yii::t('app', 'Subject'),
            'is_admin' => Yii::t('app', 'Is admin'),
            'is_user' => Yii::t('app', 'Is user'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created at'),
            'updated_at' => Yii::t('app', 'Updated at'),
            'user_agent' => Yii::t('app', 'User agent'),
            'ip' => Yii::t('app', 'Ip'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMessages()
    {
        return $this->hasMany(TicketMessages::class, ['ticket_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(Customers::class, ['id' => 'customer_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAssigned()
    {
        return $this->hasOne(SuperAdmin::class, ['id' => 'assigned_admin_id']);
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
        $this->is_admin = 1;
        return $this->save(false);
    }

    /**
     * Make readed for user ticket
     * @return bool
     */
    public function makeReaded()
    {
        $this->is_admin = 0;
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
                'customer_id' => $customerId,
                'status' => static::STATUS_PENDING
            ])->count();
    }
}
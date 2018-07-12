<?php

namespace common\models\panels;

use common\components\traits\UnixTimeFormatTrait;
use Yii;
use common\models\panels\queries\TicketMessagesQuery;
use yii\db\ActiveRecord;
use my\components\behaviors\IpBehavior;
use my\components\behaviors\UserAgentBehavior;
use yii\behaviors\TimestampBehavior;


/**
 * This is the model class for table "ticket_messages".
 *
 * @property integer $id
 * @property integer $customer_id
 * @property integer $ticket_id
 * @property integer $admin_id
 * @property string $message
 * @property integer $created_at
 * @property string $ip
 * @property int $is_system
 * @property Tickets $ticket
 * @property string $user_agent
 * @property Customers $customer
 * @property SuperAdmin $admin
 */
class TicketMessages extends ActiveRecord
{
    use UnixTimeFormatTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return DB_PANELS . '.ticket_messages';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ticket_id', 'message'], 'required'],
            [['customer_id', 'ticket_id', 'admin_id', 'created_at', 'is_system'], 'integer'],
            [['message'], 'string', 'max' => 1000],
            [['user_agent'], 'string', 'max' => 300],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'customer_id' => Yii::t('app', 'Customer id'),
            'ticket_id' => Yii::t('app', 'Ticket id'),
            'admin_id' => Yii::t('app', 'Admin id'),
            'message' => Yii::t('app', 'Message'),
            'created_at' => Yii::t('app', 'Created at'),
            'user_agent' => Yii::t('app', 'User agent'),
            'ip' => Yii::t('app', 'Ip'),
        ];
    }

    /**
     * @inheritdoc
     * @return TicketMessagesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TicketMessagesQuery(get_called_class());
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTicket()
    {
        return $this->hasOne(Tickets::class, ['id' => 'ticket_id']);
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
    public function getAdmin()
    {
        return $this->hasOne(SuperAdmin::class, ['id' => 'admin_id']);
    }

    /**
     * @param $data
     */
    public function setSystemInfo($data)
    {
        $this->is_system = true;
        $this->message = json_encode($data);
    }

    /**
     * @return mixed
     */
    public function getSystemInfo()
    {
         return json_decode($this->message);
    }

    /**
     * @return bool
     */
    public function canAdminEdit()
    {
        if (Yii::$app->superadmin->getIdentity()->id != $this->admin_id
            || $this->is_system
            || $this->customer_id
        ) {
            return false;
        }

        $query = static::find();
        $query->where([
            '>',
            'created_at', $this->created_at,
        ]);

        $query->andWhere([
            '<>',
            'admin_id', $this->admin_id,
        ]);

        $query->limit(1);
        $result = $query->one();

        if (!empty($result['id'])) {
            return false;
        }

        return true;
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'created_at',
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

}

<?php

namespace common\models\panels;

use my\components\traits\UnixTimeFormatTrait;
use Yii;
use common\models\panels\queries\TicketMessagesQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "ticket_messages".
 *
 * @property integer $id
 * @property integer $cid
 * @property integer $tid
 * @property integer $uid
 * @property string $message
 * @property integer $date
 * @property string $ip
 *
 * @property Tickets $ticket
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
        return 'ticket_messages';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tid', 'message'], 'required'],
            [['cid', 'tid', 'uid', 'date'], 'integer'],
            [['message'], 'string', 'max' => 1000],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'cid' => Yii::t('app', 'Cid'),
            'tid' => Yii::t('app', 'Tid'),
            'uid' => Yii::t('app', 'Uid'),
            'message' => Yii::t('app', 'Message'),
            'date' => Yii::t('app', 'Date'),
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
        return $this->hasOne(Tickets::className(), ['id' => 'tid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(Customers::className(), ['id' => 'cid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAdmin()
    {
        return $this->hasOne(SuperAdmin::className(), ['id' => 'uid']);
    }

}

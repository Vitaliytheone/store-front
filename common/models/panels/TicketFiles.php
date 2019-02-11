<?php

namespace common\models\panels;

use common\components\traits\UnixTimeFormatTrait;
use common\models\panels\queries\TicketFilesQuery;
use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;


/**
 * This is the model class for table "ticket_files".
 *
 * @property int $id [int(11)]
 * @property int $customer_id [int(11)]
 * @property int $ticket_id [int(11)]
 * @property int $admin_id [int(11)]
 * @property int $message_id [int(11)]
 * @property string $link [varchar(255)]
 * @property string $cdn_id [varchar(100)]
 * @property string $mime [varchar(255)]
 * @property string $details [varchar(10000)]
 * @property int $created_at [int(11)]
 *
 * @property Tickets $ticket
 * @property Customers $customer
 * @property SuperAdmin $admin

 */
class TicketFiles extends ActiveRecord
{
    use UnixTimeFormatTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return DB_PANELS . '.ticket_files';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ticket_id', 'cdn_id', 'message_id'], 'required'],
            [['customer_id', 'ticket_id', 'admin_id', 'message_id', 'created_at'], 'integer'],
            [['details'], 'string', 'max' => 10000],
            [['link', 'cdn_id', 'mime'], 'string', 'max' => 255],
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
            'message_id' => Yii::t('app', 'Message id'),
            'link' => Yii::t('app', 'Link'),
            'cdn_id' => Yii::t('app', 'Cdn ID'),
            'mime' => Yii::t('app', 'Mime'),
            'details' => Yii::t('app', 'Details'),
            'created_at' => Yii::t('app', 'Created at'),
        ];
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
        ];
    }

    /**
     * @inheritdoc
     * @return TicketFilesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TicketFilesQuery(get_called_class());
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
     * Save data array as json
     * @param $data
     */
    public function setDetails($data)
    {
        $this->details = json_encode($data);
    }

    /**
     * Get prepared array from json
     * @return array
     */
    public function getDetails(): array
    {
         return json_decode($this->details, true);
    }

    /**
     * Get all UUIDs from files group and return it as array
     * @return array
     */
    public function getPreparedIds(): array
    {
        $result = [];
        $files = json_decode($this->details, true);
        foreach ($files as $file) {
            $result[] = $file['uuid'];
        }

        return $result;
    }

}

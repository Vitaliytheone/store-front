<?php

namespace common\models\gateway;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use common\models\gateway\queries\PaymentsLogQuery;

/**
 * This is the model class for table "{{%payments_log}}".
 *
 * @property int $id
 * @property int $payment_id
 * @property string $response
 * @property string $ip
 * @property string $user_agent
 * @property int $created_at
 *
 * @property Payments $payment
 */
class PaymentsLog extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%payments_log}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['payment_id', 'response', 'created_at'], 'required'],
            [['payment_id', 'created_at'], 'integer'],
            [['response'], 'string'],
            [['ip', 'user_agent'], 'string', 'max' => 300],
            [['payment_id'], 'exist', 'skipOnError' => true, 'targetClass' => Payments::class, 'targetAttribute' => ['payment_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'payment_id' => Yii::t('app', 'Payment ID'),
            'response' => Yii::t('app', 'Response'),
            'ip' => Yii::t('app', 'Ip'),
            'user_agent' => Yii::t('app', 'User Agent'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPayment()
    {
        return $this->hasOne(Payments::class, ['id' => 'payment_id']);
    }

    /**
     * @inheritdoc
     * @return PaymentsLogQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PaymentsLogQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
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
}
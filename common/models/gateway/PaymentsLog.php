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
 * @property string $result
 * @property string $ip
 * @property string $user_agent
 * @property int $created_at
 *
 * @property Payments $payment
 */
class PaymentsLog extends ActiveRecord
{

    public static function getDb()
    {
        return Yii::$app->gatewayDb;
    }

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
            [['payment_id'], 'required'],
            [['payment_id', 'created_at'], 'integer'],
            [['response', 'result'], 'string'],
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
            'result' => Yii::t('app', 'Result'),
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

    /**
     * Set response
     * @param array|string $response
     */
    public function setResponse($response)
    {
        $this->response = is_string($response) ? $response : @json_encode($response);
    }

    /**
     * Get response
     * @return array|string
     */
    public function getResponse()
    {
        return ($response = @json_decode($this->response, true)) ? $response : $this->response;
    }

    /**
     * Set result
     * @param array|string $result
     * @return static
     */
    public function setResult($result)
    {
        $this->result = is_string($result) ? $result : @json_encode($result);

        return $this;
    }

    /**
     * Get result
     * @return array|string
     */
    public function getResult()
    {
        return ($result = @json_decode($this->result, true)) ? $result : $this->result;
    }
}
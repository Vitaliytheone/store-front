<?php

namespace common\models\panels;


use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use Yii;
use common\models\panels\queries\PaypalFraudResponseQuery;

/**
 * This is the model class for table "{{%paypal_fraud_response}}".
 *
 * @property int $id
 * @property int $panel_id
 * @property int $payment_id
 * @property string $response PayPal server response
 * @property int $created_at
 */
class PaypalFraudResponse extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%paypal_fraud_response}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['panel_id', 'payment_id', 'response'], 'required'],
            [['panel_id', 'payment_id', 'created_at'], 'integer'],
            [['response'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'panel_id' => Yii::t('app', 'Panel ID'),
            'payment_id' => Yii::t('app', 'Payment ID'),
            'response' => Yii::t('app', 'PayPal server response'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return PaypalFraudResponseQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PaypalFraudResponseQuery(get_called_class());
    }

    /**
     * {@inheritdoc}
     * @return array
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
     * @param $response
     */
    public function setResponse($response)
    {
        $this->response = json_encode($response, JSON_PRETTY_PRINT);
    }

    /**
     * Get response
     * @return mixed
     */
    public function getResponse()
    {
        return json_decode($this->response, true);
    }
}

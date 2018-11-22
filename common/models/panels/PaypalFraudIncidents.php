<?php

namespace common\models\panels;


use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use Yii;
use common\models\panels\queries\PaypalFraudIncidentsQuery;

/**
 * This is the model class for table "{{%paypal_fraud_incidents}}".
 *
 * @property int $id
 * @property int $panel_id
 * @property int $payment_id
 * @property int $fraud_risk 0 - undefined, 1 - high, 2 - critical
 * @property string $fraud_reason 0 - нет, 1 - unverifed, 2 - high from fraud_accounts id#id записи из таблицы paypal_fraud_accounts, 3 - critical from fraud_accounts id#id записи из таблицы paypal_fraud_accounts
 * @property int $balance_added 0 - not added on balance, 1 - funds added on balance
 * @property int $created_at
 */
class PaypalFraudIncidents extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%paypal_fraud_incidents}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['panel_id', 'payment_id', 'fraud_risk', 'balance_added'], 'required'],
            [['panel_id', 'payment_id', 'fraud_risk', 'balance_added', 'created_at'], 'integer'],
            [['fraud_reason'], 'string', 'max' => 1000],
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
            'fraud_risk' => Yii::t('app', '0 - undefined, 1 - high, 2 - critical'),
            'fraud_reason' => Yii::t('app', '0 - нет, 1 - unverifed, 2 - high from fraud_accounts id#id записи из таблицы paypal_fraud_accounts, 3 - critical from fraud_accounts id#id записи из таблицы paypal_fraud_accounts'),
            'balance_added' => Yii::t('app', '0 - not added on balance, 1 - funds added on balance'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return PaypalFraudIncidentsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PaypalFraudIncidentsQuery(get_called_class());
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
     * Set fraud reason value
     * @param $fraudReason
     * @param $paypalFraudAccountId
     */
    public function setFraudReason($fraudReason, $paypalFraudAccountId = null)
    {
        $this->fraud_reason = $fraudReason;

        if ($paypalFraudAccountId) {
            $this->fraud_reason .= '#' . $paypalFraudAccountId;
        }
    }

    /**
     * Get fraud reason value
     * @return array|mixed
     */
    public function getFraudReason()
    {
        $reason = explode('#', $this->fraud_reason);

        if (!is_array($reason)) {
            return  $this->fraud_reason;
        }

        return [
            'fraud_reason' => $reason[0],
            'paypal_fraud_account_id' => isset($reason[1]) ? $reason[1] : null
        ];
    }
}
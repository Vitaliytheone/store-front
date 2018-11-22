<?php

namespace common\models\panels;


use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use Yii;
use common\models\panels\queries\PaypalFraudAccountsQuery;

/**
 * This is the model class for table "{{%paypal_fraud_accounts}}".
 *
 * @property int $id
 * @property string $payer_id PayPal payer ID
 * @property string $payer_email PayPal payer email
 * @property int $fraud_risk 1 - high, 2 - critical
 * @property int $payer_status 1 - verified, 0 - unverified
 * @property int $created_at
 * @property int $updated_at
 */
class PaypalFraudAccounts extends ActiveRecord
{
    const PAYER_STATUS_UNVERIFIED = 0;
    const PAYER_STATUS_VERIFIED = 1;

    const FRAUD_RISK_HIGH = 1;
    const FRAUD_RISK_CRITICAL = 2;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%paypal_fraud_accounts}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fraud_risk', 'payer_status'], 'required'],
            [['fraud_risk', 'payer_status', 'created_at', 'updated_at'], 'integer'],
            [['payer_id', 'payer_email'], 'string', 'max' => 1000],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'payer_id' => Yii::t('app', 'PayPal payer ID'),
            'payer_email' => Yii::t('app', 'PayPal payer email'),
            'fraud_risk' => Yii::t('app', '1 - high, 2 - critical'),
            'payer_status' => Yii::t('app', '1 - verified, 0 - unverified'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return PaypalFraudAccountsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PaypalFraudAccountsQuery(get_called_class());
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => [
                        'created_at',
                        'updated_at'
                    ],
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'updated_at',
                ],
                'value' => function() {
                    return time();
                },
            ],
        ];
    }
}

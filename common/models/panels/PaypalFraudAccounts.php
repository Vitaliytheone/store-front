<?php

namespace common\models\panels;


use common\components\traits\UnixTimeFormatTrait;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use Yii;
use common\models\panels\queries\PaypalFraudAccountsQuery;
use yii\helpers\ArrayHelper;

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

    use UnixTimeFormatTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'paypal_fraud_accounts';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['fraud_risk', 'payer_status'], 'required'],
            [['fraud_risk', 'payer_status', 'created_at', 'updated_at'], 'integer'],
            [['payer_id', 'payer_email'], 'string', 'max' => 1000],
            [['fraud_risk', 'payer_status'], 'string', 'max' => 1],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'payer_id' => Yii::t('app', 'Payer ID'),
            'payer_email' => Yii::t('app', 'Payer email'),
            'fraud_risk' => Yii::t('app', 'Fraud Risk'),
            'payer_status' => Yii::t('app', 'Payer status'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return PaypalFraudAccountsQuery|\yii\db\ActiveQuery
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

    /**
     * Get risk list
     * @return array
     */
    public static function getRisks(): array
    {
        return [
            static::FRAUD_RISK_HIGH => Yii::t('app/superadmin', 'paypal_fraud_accounts.risk.high'),
            static::FRAUD_RISK_CRITICAL => Yii::t('app/superadmin', 'paypal_fraud_accounts.risk.critical'),
        ];
    }

    /**
     * Get status list
     * @return array
     */
    public static function getStatuses(): array
    {
        return [
            static::PAYER_STATUS_UNVERIFIED => Yii::t('app/superadmin', 'paypal_fraud_accounts.status.unverified'),
            static::PAYER_STATUS_VERIFIED => Yii::t('app/superadmin', 'paypal_fraud_accounts.status.verified'),
        ];
    }

    /**
     * Get risk name
     * @param int $risk
     * @return string
     */
    public static function getRiskName(int $risk): string
    {
        return ArrayHelper::getValue(static::getRisks(), $risk, '');
    }

    /**
     * Get status name
     * @param int $status
     * @return string
     */
    public static function getStatusName(int $status): string
    {
        return ArrayHelper::getValue(static::getStatuses(), $status, '');
    }
}

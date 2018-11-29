<?php

namespace common\models\panels;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use common\models\panels\queries\PaypalFraudReportsQuery;

/**
 * This is the model class for table "paypal_fraud_reports".
 *
 * @property int $id
 * @property int $panel_id
 * @property int $user_id
 * @property int $payment_id
 * @property string $report
 * @property int $status 0 - pending, 1 - accepted, 2 - rejected
 * @property int $created_at
 * @property int $updated_at
 * @property string $transaction_details
 */
class PaypalFraudReports extends ActiveRecord
{

    const STATUS_PENDING = 0;
    const STATUS_ACCEPTED = 1;
    const STATUS_REJECTED = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'paypal_fraud_reports';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'report', 'status'], 'required'],
            [['id', 'panel_id', 'user_id', 'payment_id', 'created_at', 'updated_at'], 'integer'],
            [['report', 'transaction_details'], 'string'],
            [['status'], 'string', 'max' => 1],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'panel_id' => Yii::t('app', 'Panel ID'),
            'user_id' => Yii::t('app', 'User ID'),
            'payment_id' => Yii::t('app', 'Payment ID'),
            'report' => Yii::t('app', 'Report'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'transaction_details' => Yii::t('app', 'Transaction Details'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return PaypalFraudReportsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PaypalFraudReportsQuery(get_called_class());
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
     * @return array
     */
    public static function getStatuses(): array
    {
        return [
            static::STATUS_PENDING => Yii::t('app/superadmin', 'fraud_reports.status.pending'),
            static::STATUS_ACCEPTED => Yii::t('app/superadmin', 'fraud_reports.status.accepted'),
            static::STATUS_REJECTED => Yii::t('app/superadmin', 'fraud_reports.status.rejected'),
        ];
    }

    /**
     * @param int $status
     * @return string
     */
    public static function getStatusName(int $status): string
    {
        return ArrayHelper::getValue(static::getStatuses(), $status, '');
    }

    /**
     * @param int $status
     * @return bool
     * @throws \yii\db\Exception
     */
    public function changeStatus(int $status): bool
    {
        if (!array_key_exists($status, static::getStatuses())) {
            return false;
        }

        switch ($status) {
            case static::STATUS_ACCEPTED :
                $transaction = Yii::$app->db->beginTransaction();

                $this->status = $status;
                $this->save(false);

                $details = $this->getDetails();

                if (empty($details)) {
                    $transaction->commit();
                    return true;
                }

                if (PaypalFraudAccounts::find()
                    ->andWhere([
                        'payer_id' => $details['PAYERID'],
                        'payer_email' => $details['EMAIL'],
                    ])
                    ->exists()) {
                    $transaction->commit();
                    return true;
                }

                $fraudAccount = new PaypalFraudAccounts([
                    'payer_id' => $details['PAYERID'],
                    'payer_email' => $details['EMAIL'],
                    'lastname' => $details['LASTNAME'],
                    'firstname' => $details['FIRSTNAME'],
                    'fraud_risk' => PaypalFraudAccounts::FRAUD_RISK_CRITICAL,
                    'payer_status' => strtolower($details['PAYERSTATUS']) == 'verified' ?
                        PaypalFraudAccounts::PAYER_STATUS_VERIFIED :
                        PaypalFraudAccounts::PAYER_STATUS_UNVERIFIED,
                ]);

                if (!$fraudAccount->save()) {
                    $transaction->rollBack();
                    return false;
                }

                $transaction->commit();
                return true;

            default:
                $this->status = $status;
                return $this->save(false);
        }
    }

    /**
     * Set transaction_details
     * @param array $details
     */
    public function setDetails(array $details)
    {
        $this->transaction_details = json_encode($details);
    }

    /**
     * Get decode transaction_details
     * @return array|null
     */
    public function getDetails()
    {
        return json_decode($this->transaction_details, true);
    }
}

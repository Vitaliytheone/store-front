<?php

namespace common\models\panels;

use common\components\traits\UnixTimeFormatTrait;
use my\helpers\PaymentsHelper;
use Yii;
use yii\base\Exception;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use common\models\panels\queries\PaymentsQuery;
use yii\base\Security;

/**
 * This is the model class for table "{{%payments}}".
 *
 * @property integer $id
 * @property integer $pid
 * @property integer $iid
 * @property string $comment
 * @property string $transaction_id
 * @property integer $date
 * @property integer $date_update
 * @property integer $type
 * @property integer $payment_method
 * @property string $amount
 * @property string $fee
 * @property integer $status
 * @property string $ip
 * @property integer $response
 * @property integer $mode
 * @property string $options
 * @property string $verification_code
 *
 * @property Project $project
 * @property Params $method
 * @property PaymentsLog[] $paymentLogs
 * @property Invoices $invoice
 * @property InvoiceDetails $invoiceDetails
 */
class Payments extends ActiveRecord
{
    const STATUS_PENDING = 0;
    const STATUS_FAIL = 3;
    const STATUS_COMPLETED = 1;
    const STATUS_WAIT = 2;
    const STATUS_REFUNDED = 4;
    const STATUS_EXPIRED = 5;
    const STATUS_REVIEW = 6;
    const STATUS_VERIFICATION = 7;
    const STATUS_UNVERIFIED = 8;
    const STATUS_REVERSED = 9;

    const MODE_MANUAL = 0;
    const MODE_AUTO = 1;

    use UnixTimeFormatTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return DB_PANELS . '.payments';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pid', 'iid', 'date', 'type', 'amount', 'ip'], 'required'],
            [['pid', 'iid', 'date', 'date_update', 'type', 'status', 'response', 'mode'], 'integer'],
            [['amount', 'fee'], 'number'],
            [['comment'], 'string', 'max' => 1000],
            [['ip', 'transaction_id'], 'string', 'max' => 300],
            [['mode'], 'default', 'value' => static::MODE_AUTO],
            [['options'], 'string'],
            [['verification_code', 'payment_method'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'pid' => Yii::t('app', 'Pid'),
            'iid' => Yii::t('app', 'Iid'),
            'comment' => Yii::t('app', 'Comment'),
            'transaction_id' => Yii::t('app', 'Transaction ID'),
            'date' => Yii::t('app', 'Date'),
            'date_update' => Yii::t('app', 'Date Update'),
            'type' => Yii::t('app', 'Type'),
            'payment_method' => Yii::t('app', 'Payment Method'),
            'amount' => Yii::t('app', 'Amount'),
            'fee' => Yii::t('app', 'Fee'),
            'status' => Yii::t('app', 'Status'),
            'ip' => Yii::t('app', 'Ip'),
            'response' => Yii::t('app', 'Response'),
            'mode' => Yii::t('app', 'Mode'),
            'options' => Yii::t('app', 'Options'),
            'verification_code' => Yii::t('app', 'Verification code'),
        ];
    }

    /**
     * @inheritdoc
     * @return PaymentsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PaymentsQuery(get_called_class());
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'date',
                ],
                'value' => function() {
                    return time();
                },
            ]
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPaymentLogs()
    {
        return $this->hasMany(PaymentsLog::class, ['pid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvoice()
    {
        return $this->hasOne(Invoices::class, ['id' => 'iid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvoiceDetails()
    {
        return $this->hasMany(InvoiceDetails::class, ['invoice_id' => 'id'])->via('invoice');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProject()
    {
        return $this->hasOne(Project::class, ['id' => 'pid']);
    }

    /**
     * Get modes
     * @return array
     */
    public static function getModes()
    {
        return [
            static::MODE_MANUAL => Yii::t('app', 'payments.mode.manual'),
            static::MODE_AUTO => Yii::t('app', 'payments.mode.auto'),
        ];
    }

    /**
     * Get statuses
     * @return array
     */
    public static function getStatuses()
    {
        return [
            static::STATUS_PENDING => Yii::t('app', 'payments.status.pending'),
            static::STATUS_COMPLETED => Yii::t('app', 'payments.status.completed'),
            static::STATUS_WAIT => Yii::t('app', 'payments.status.wait'),
            static::STATUS_FAIL => Yii::t('app', 'payments.status.fail'),
            static::STATUS_REFUNDED => Yii::t('app', 'payments.status.refunded'),
            static::STATUS_EXPIRED => Yii::t('app', 'payments.status.expired'),
            static::STATUS_REVIEW => Yii::t('app', 'payments.status.review'),
            static::STATUS_VERIFICATION => Yii::t('app', 'payments.status.verification'),
            static::STATUS_UNVERIFIED => Yii::t('app', 'payments.status.unverified'),
            static::STATUS_REVERSED  => Yii::t('app', 'payments.status.reversed'),
        ];
    }

    /**
     * Get status string name
     * @return string
     */
    public function getStatusName()
    {
        return ArrayHelper::getValue(static::getStatuses(), $this->status);
    }

    /**
     * Get mode string name
     * @return string
     */
    public function getModeName()
    {
        return ArrayHelper::getValue(static::getModes(), $this->mode);
    }

    /**
     * Get payment type name
     * @return mixed
     */
    public function getTypeName()
    {
        return ($name = Params::get(Params::CATEGORY_PAYMENT, $this->payment_method)) ? $name : Yii::t('app', 'payment_gateway.method.other');
    }

    /**
     * Get payment method name
     * @return mixed
     */
    public function getMethodName()
    {
        return ArrayHelper::getValue($this->method, 'name', Yii::t('app', 'payment_gateway.method.other'));
    }

    /**
     * Set payment options
     * @param array $options
     */
    public function setOptions($options)
    {
        $this->options = json_encode($options);
    }

    /**
     * Get payment options
     * @return  array
     */
    public function getOptions()
    {
        return json_decode($this->options, true);
    }

    /**
     * Can do something
     * @param string $code
     * @return boolean
     */
    public function can($code)
    {
        switch ($code) {
            case 'makeActive':
                if (in_array($this->payment_method, [
                    Params::CODE_TWO_CHECKOUT,
                    Params::CODE_PAYPAL
                ])) {

                    if (Payments::STATUS_WAIT == $this->status && $this->pid) {
                        return true;
                    }

                }
            break;

            case 'makeNotActive':
                if (in_array($this->payment_method, [
                    Params::CODE_TWO_CHECKOUT,
                    Params::CODE_PAYPAL
                ])) {

                    if (Payments::STATUS_REVIEW == $this->status && $this->pid) {
                        return true;
                    }

                }
            break;

            case 'makeAccepted':
                if (in_array($this->payment_method, [Params::CODE_PAYPAL]) &&
                    $this->status == self::STATUS_VERIFICATION
                ) {
                    return true;
                }
            break;

            case 'makeRefunded':
                if (in_array($this->payment_method, [Params::CODE_PAYPAL]) &&
                    $this->status == self::STATUS_VERIFICATION &&
                    !empty($this->transaction_id) &&
                     time() < $this->date_update + Params::PAYPAL_REFUND_EXPIRED_AFTER
                ) {
                    return true;
                }
            break;

            case 'makeCompleted':
                if ($this->status == self::STATUS_FAIL) {
                    return true;
                }
            break;
        }
        return false;
    }

    /**
     * Make active payment and activate project
     * @return bool
     */
    public function makeActive()
    {
        if (!$this->can('makeActive')) {
            return false;
        }

        $project = $this->project;

        $this->status = static::STATUS_REVIEW;
        $this->setOptions([
            'project_status' => $project->act,
            'project_expired' => $project->expired,
        ]);
        $this->save(false);

        $lastExpired = $project->expired;

        $project->generateExpired();
        $project->act = Project::STATUS_ACTIVE;
        $project->save(false);

        $ExpiredLogModel = new ExpiredLog();
        $ExpiredLogModel->attributes = [
            'pid' => $project->id,
            'expired_last' => $lastExpired,
            'expired' => $project->expired,
            'created_at' => time(),
            'type' => ExpiredLog::TYPE_CHANGE_EXPIRY
        ];
        $ExpiredLogModel->save(false);
    }

    /**
     * Make not active payment and activate project
     * @return bool
     */
    public function makeNotActive()
    {
        if (!$this->can('makeNotActive')) {
            return false;
        }

        $options = $this->getOptions();

        $this->status = static::STATUS_FAIL;
        $this->save(false);

        $this->project->expired = ArrayHelper::getValue($options, 'project_expired', $this->project->expired);
        $this->project->act = ArrayHelper::getValue($options, 'project_status', $this->project->act);
        $this->project->save(false);
    }

    /**
     * Change payment status
     * @param int $status
     */
    public function changeStatus($status)
    {
        switch ($status) {
            case static::STATUS_REVIEW:
                if ($this->can('makeActive')) {
                    $this->status = static::STATUS_REVIEW;
                }
            break;
        }

        $this->save(false);
    }

    /**
     * Complete payment with invoice and invoice details
     * @throws \Throwable
     * @throws \yii\db\Exception
     * @throws \yii\db\StaleObjectException
     */
    public function complete()
    {
        $invoice = $this->invoice;

        // Mark invoice paid
        if (Payments::STATUS_REVIEW == $this->status) {
            $invoice->markPaid();
        } else {
            $invoice->paid($this->payment_method);
        }

        $this->status = static::STATUS_COMPLETED;
        $this->update(false);
    }

    /**
     * Make payment as `Payer verification needed`
     * @param $payerId
     * @param $payerEmail
     * @return string
     * @throws Exception
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function verification($payerId, $payerEmail)
    {
        if ($this->payment_method != Params::CODE_PAYPAL) {
            throw new Exception('This method for PayPal payments only!');
        }

        $this->status = Payments::STATUS_VERIFICATION;

        // Generate unique verification code
        do {
            $code = (new Security)->generateRandomString(32);
        } while(static::findOne(['verification_code' => $code]));

        $this->verification_code = $code;
        $this->update(false);

        if (filter_var($payerEmail, FILTER_VALIDATE_EMAIL)) {
            $verification = new MyVerifiedPaypal();
            $verification->payment_id = $this->id;
            $verification->paypal_payer_id = $payerId;
            $verification->paypal_payer_email = $payerEmail;
            $verification->verified = MyVerifiedPaypal::STATUS_NOT_VERIFIED;
            $verification->save(false);
        }

        return $this->verification_code;
    }

    /**
     * Make payment as verified and completed
     * @throws Exception
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function verified()
    {
        if ($this->payment_method != Params::CODE_PAYPAL) {
            throw new Exception('This method for PayPal payments only!');
        }

        $verify = MyVerifiedPaypal::findOne(['payment_id' => $this->id]);

        if (!$verify) {
            throw new Exception('Payment verification data not found');
        }

        $verify->verified = MyVerifiedPaypal::STATUS_VERIFIED;
        $verify->update(false);

        $this->complete();
    }

    /**
     * Make payment refunded
     * @return bool
     * @throws Exception
     */
    public function refund()
    {
        if ($this->payment_method != Params::CODE_PAYPAL) {
            throw new Exception('This method for PayPal payments only!');
        }

        if (PaymentsHelper::refundPaypalPayment($this)) {
            $this->status = Payments::STATUS_UNVERIFIED;
            $this->save(false);
        }
    }
}

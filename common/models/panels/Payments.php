<?php

namespace common\models\panels;

use my\components\traits\UnixTimeFormatTrait;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use common\models\panels\queries\PaymentsQuery;

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
 * @property string $amount
 * @property integer $status
 * @property string $ip
 * @property integer $response
 * @property integer $mode
 * @property string $options
 *
 * @property Project $project
 * @property PaymentGateway $method
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

    const MODE_MANUAL = 0;
    const MODE_AUTO = 1;

    use UnixTimeFormatTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'payments';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pid', 'iid', 'date', 'type', 'amount', 'ip'], 'required'],
            [['pid', 'iid', 'date', 'date_update', 'type', 'status', 'response', 'mode'], 'integer'],
            [['amount'], 'number'],
            [['comment'], 'string', 'max' => 1000],
            [['ip', 'transaction_id'], 'string', 'max' => 300],
            [['mode'], 'default', 'value' => static::MODE_AUTO],
            [['options'], 'string'],
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
            'amount' => Yii::t('app', 'Amount'),
            'status' => Yii::t('app', 'Status'),
            'ip' => Yii::t('app', 'Ip'),
            'response' => Yii::t('app', 'Response'),
            'mode' => Yii::t('app', 'Mode'),
            'options' => Yii::t('app', 'Options'),
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
                'class' => TimestampBehavior::className(),
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
    public function getMethod()
    {
        return $this->hasOne(PaymentGateway::className(), ['pgid' => 'type'])
            ->andOnCondition([ 'payment_gateway.pid' => '-1']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPaymentLogs()
    {
        return $this->hasMany(PaymentsLog::className(), ['pid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvoice()
    {
        return $this->hasOne(Invoices::className(), ['id' => 'iid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvoiceDetails()
    {
        return $this->hasMany(InvoiceDetails::className(), ['invoice_id' => 'id'])->via('invoice');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProject()
    {
        return $this->hasOne(Project::className(), ['id' => 'pid']);
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
        return ArrayHelper::getValue(PaymentGateway::getMethods(), $this->type, Yii::t('app', 'payment_gateway.method.other'));
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
                if (in_array($this->type, [
                    PaymentGateway::METHOD_TWO_CHECKOUT,
                    PaymentGateway::METHOD_PAYPAL
                ])) {

                    if (Payments::STATUS_WAIT == $this->status && $this->pid) {
                        return true;
                    }

                }
            break;

            case 'makeNotActive':
                if (in_array($this->type, [
                    PaymentGateway::METHOD_TWO_CHECKOUT,
                    PaymentGateway::METHOD_PAYPAL
                ])) {

                    if (Payments::STATUS_REVIEW == $this->status && $this->pid) {
                        return true;
                    }

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
     */
    public function complete()
    {
        $invoice = $this->invoice;

        // Mark invoice paid
        if (Payments::STATUS_REVIEW == $this->status) {
            $invoice->markPaid();
        } else {
            $invoice->paid($this->type);
        }

        $this->status = static::STATUS_COMPLETED;
        $this->update();
    }
}
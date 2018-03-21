<?php

namespace common\models\panels;

use my\helpers\StringHelper;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use my\components\traits\UnixTimeFormatTrait;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%invoices}}".
 *
 * @property integer $id
 * @property integer $cid
 * @property string $code
 * @property integer $date
 * @property integer $date_update
 * @property integer $expired
 * @property string $total
 * @property string $credit
 * @property integer $status
 *
 * @property Customers $customer
 * @property InvoiceDetails[] $invoiceDetails
 * @property Payments $lastPayment
 */
class Invoices extends ActiveRecord
{
    const STATUS_UNPAID = 0;
    const STATUS_PAID = 1;
    const STATUS_CANCELED = 2;

    use UnixTimeFormatTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'invoices';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['code', 'expired', 'total', 'cid'], 'required'],
            [['date', 'date_update', 'expired', 'status', 'cid'], 'integer'],
            [['status'], 'default', 'value' => static::STATUS_UNPAID],
            [['total', 'credit'], 'number'],
            [['code'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'cid' => 'Cid',
            'code' => 'Code',
            'date' => 'Date',
            'date_update' => 'Date Update',
            'expired' => 'Expired',
            'total' => 'Total',
            'credit' => 'Credit',
            'status' => 'Status',
        ];
    }

    /**
     * Generate unique code
     */
    public function generateCode()
    {
        $code = StringHelper::hash();
        $result = static::findOne(['code' => $code]);
        if($result !== null) {
            $this->generateCode();
        } else {
            $this->code = $code;
        }
    }

    /**
     * Generate days expired
     */
    public function daysExpired($days)
    {
        $this->expired = time() + (int)$days * (24 * 60 * 60);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(Customers::className(), ['id' => 'cid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvoiceDetails()
    {
        return $this->hasMany(InvoiceDetails::className(), ['invoice_id' => 'id']);
    }

    /**
     * Get status labels
     * @return array
     */
    public static function getStatusLabels()
    {
        return [
            static::STATUS_UNPAID => Yii::t('app', 'invoices.status.unpaid'),
            static::STATUS_PAID => Yii::t('app', 'invoices.status.paid'),
            static::STATUS_CANCELED => Yii::t('app', 'invoices.status.canceled'),
        ];
    }

    /**
     * Get invoice status depended of payment status
     * @return int
     */
    public function isWait()
    {
        if (static::STATUS_UNPAID == $this->status) {
            $payment = Payments::findOne([
                'iid' => $this->id,
                'status' => Payments::STATUS_WAIT
            ]);

            if ($payment) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get invoice status depended of payment status
     * @return int
     */
    public function getStatus()
    {
        if (static::STATUS_PAID == $this->status) {
            if ($this->isWait()) {
                return static::STATUS_UNPAID;
            }
        }

        return $this->status;
    }

    /**
     * Get status name
     * @return string
     */
    public function getStatusName()
    {
        return static::getStatusLabels()[$this->status];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLastPayment()
    {
        return $this->hasOne(Payments::className(), ['iid' => 'id'])->orderBy([
            'payments.id' => SORT_DESC
        ]);
    }

    /**
     * Paid invoice
     * @param $method
     * @return bool
     */
    public function paid($method)
    {
        $transaction = Yii::$app->db->beginTransaction();

        foreach ($this->invoiceDetails as $detail) {
            if (!$detail->paid($method)) {
                $transaction->rollBack();
                return false;
            }
        }

        $result = $this->markPaid();

        if ($result) {
            $transaction->commit();
        } else {
            $transaction->rollBack();
        }

        return $result;
    }

    /**
     * Mark invoice as paid
     * @return bool
     */
    public function markPaid()
    {
        $this->status = static::STATUS_PAID;
        $result = $this->save(false);

        $activateReferral = $activateChildPanels = false;
        $customer = $this->customer;

        if ($result && $customer) {

            $invoiceDetails = $this->invoiceDetails;

            // Отменяем неоплаченные заказы на покупку панелей с тем же доменом заказа
            $cancelPendingOrders = function (InvoiceDetails $invoiceDetails) {
                $order = $invoiceDetails->order;

                /**
                 * @var Orders $oldOrder
                 */
                $oldOrder = Orders::find()
                    ->andWhere([
                        'domain' => $order->domain,
                        'status' => Orders::STATUS_PENDING
                    ])
                    ->andWhere('id <> ' . $order->id)
                    ->one();

                if ($oldOrder) {
                    $oldOrder->cancel();
                }
            };


            foreach ($invoiceDetails as $detail) {

                if (in_array($detail->item, [
                    InvoiceDetails::ITEM_BUY_PANEL,
                    InvoiceDetails::ITEM_BUY_CHILD_PANEL,
                ])) {
                    $cancelPendingOrders($detail);
                }

                if (in_array($detail->item, [
                    InvoiceDetails::ITEM_BUY_PANEL,
                    InvoiceDetails::ITEM_PROLONGATION_PANEL
                ])) {
                    if (!$customer->can('referral') && !$activateReferral) {
                        $activateReferral = true;
                        $customer->activateReferral();
                    }

                    if (!$customer->can('child') && !$activateChildPanels) {
                        $activateChildPanels = true;
                        $customer->activateChildPanels();
                    }

                    $referrer = $customer->referrer;

                    if (!$referrer) {
                        continue;
                    }

                    if (!$customer->can('pay_referral')) {
                        continue;
                    }

                    $earnings = ($detail->amount / 100) * Yii::$app->params['referral_percent'];

                    $referralEarning = new ReferralEarnings();
                    $referralEarning->customer_id = $referrer->id;
                    $referralEarning->earnings = $earnings;
                    $referralEarning->invoice_id = $this->id;
                    $referralEarning->save(false);

                    $referrer->unpaid_earnings += $earnings;
                    $referrer->save(false);
                }
            }
        }

        return $result;
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
     * Check access by code
     * @param string $code
     * @return bool
     */
    public function can($code)
    {
        switch ($code) {
            case 'editTotal':
                $details = $this->invoiceDetails;

                if (count($details) > 1) {
                    return false;
                }

                foreach ($details as $item) {
                    if ($item->item != InvoiceDetails::ITEM_PROLONGATION_PANEL) {
                        return false;
                    }
                    break;
                }

                return true;
            break;

            case 'pay':
                if (static::STATUS_UNPAID == $this->status) {
                    return true;
                }
            break;
        }

        return false;
    }

    /**
     * Get help notes
     * @return array
     */
    public function getNotesByPaymentMethods()
    {
        $notes = [];

        if ($this->isWait()) {
            $notes = [
                PaymentGateway::METHOD_PAYPAL => Content::getContent('paypal_hold'),
                PaymentGateway::METHOD_TWO_CHECKOUT => Content::getContent('2checkout_review'),
                PaymentGateway::METHOD_BITCOIN => Content::getContent('bitcoin_not_confirmed'),
                PaymentGateway::METHOD_COINPAYMENTS => Content::getContent('coinpayments_not_confirmed'),
            ];
        } else if (static::STATUS_UNPAID == $this->status) {
            $notes = [
                PaymentGateway::METHOD_PAYPAL => Content::getContent('paypal_note'),
                PaymentGateway::METHOD_TWO_CHECKOUT => Content::getContent('2checkout_note'),
                PaymentGateway::METHOD_BITCOIN => Content::getContent('bitcoin_note'),
                PaymentGateway::METHOD_COINPAYMENTS => Content::getContent('coinpayments_note'),
            ];
        }

        return $notes;
    }

    /**
     * Get payment amount
     * @return float
     */
    public function getPaymentAmount()
    {
        $total = $this->total - $this->credit;
        return $total > 0 ? $total : 0;
    }

    /**
     * Get invoice details with
     * @return InvoiceDetails[]|array
     */
    public function getCountedInvoiceDetails()
    {
        $credit = $this->credit;

        if ($credit <= 0) {
            return $this->invoiceDetails;
        }

        $items = InvoiceDetails::find()->andWhere([
            'invoice_id' => $this->id
        ])->orderBy([
            'amount' => SORT_DESC
        ])->all();


        foreach ($items as &$item) {
            if ($credit <= 0) {
                continue;
            }

            $amount = $item->amount;

            if ($amount > $credit) {
                $amount -= $credit;
                $credit = 0;
            } else {
                $credit -= $amount;
                $amount = 0;
            }

            $item->amount = $amount;
        }

        return $items;
    }
}
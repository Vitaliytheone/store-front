<?php
namespace console\components\payments;

use common\helpers\PaymentHelper;
use common\models\panels\Params;
use common\models\panels\Payments;
use my\components\payments\Paypal;
use my\components\payments\TwoCheckout;
use yii\helpers\ArrayHelper;

/**
 * Class PaymentsFee
 * @package console\components\payments
 */
class PaymentsFee {

    /**
     * @var string
     */
    protected $from;

    /**
     * @var string
     */
    protected $to;

    /**
     * @var int
     */
    protected $days;

    /**
     * @var array
     */
    protected $types;

    /**
     * @var array
     */
    protected static $availableTypes = [
        PaymentHelper::TYPE_PAYPAL,
        PaymentHelper::TYPE_TWO_CHECKOUT
    ];

    public function __construct($days = null, $from = null, $to = null, $types = [])
    {
        $this->from = (string)$from;
        $this->to = (string)$to;
        $this->days = (int)$days;
        $this->types = empty($types) ? static::$availableTypes : $types;
    }

    public function run()
    {
        $query = Payments::find()
            ->andWhere([
                'type' => $this->types,
                'response' => 1,
                'status' => Payments::STATUS_COMPLETED
            ])
            ->andWhere('fee IS NULL AND transaction_id IS NOT NULL');

        if (!empty($this->days)) {
            $query->andWhere('date > :from', [
                ':from' => time() - $this->days * 86400
            ]);
        }

        if (!empty($this->from) && !empty($this->to)) {
            $query->andWhere(['between', 'date', strtotime($this->from), strtotime($this->to)]);
        } elseif (!empty($this->from)) {
            $query->andWhere('date > :from', [
                ':from' => strtotime($this->from)
            ]);
        } elseif (!empty($this->from)) {
            $query->andWhere('date < :to', [
                ':to' => strtotime($this->to)
            ]);
        }

        foreach ($query->batch() as $payments) {
            foreach ($payments as $payment) {
                /**
                 * @var Payments $payment
                 */
                switch ($payment->payment_method) {
                    case Params::CODE_PAYPAL:
                        $this->paypal($payment);
                    break;

                    case Params::CODE_TWO_CHECKOUT:
                        $this->twoCheckout($payment);
                    break;
                }
            }
        }
    }

    /**
     * @param Payments $payment
     */
    public function paypal(Payments $payment)
    {
        $paypal = new Paypal();

        $getTransactionDetails = $paypal->request('GetTransactionDetails', array(
            'TRANSACTIONID' => $payment->transaction_id
        ));

        $fee = ArrayHelper::getValue($getTransactionDetails, 'FEEAMT');

        if (empty($fee)) {
            return;
        }

        $payment->fee = $fee;
        $payment->save(false);
    }

    /**
     * @param Payments $payment
     */
    public function twoCheckout(Payments $payment)
    {
        $twoCheckout = new TwoCheckout();

        $getFee = $twoCheckout->detailSale(array(
           'sale_id' => $payment->transaction_id
        ));

        $fee = (float)$getFee;

        if (empty($fee)) {
            return;
        }

        $payment->fee = $fee;
        $payment->save(false);
    }
}
<?php

namespace store\mail\mailers;

use common\models\store\Checkouts;
use common\models\store\Orders;
use common\models\store\Payments;
use common\models\store\Suborders;
use common\models\stores\PaymentMethods;
use yii\base\InvalidParamException;
use yii\helpers\ArrayHelper;

/**
 * Class OrderMailer
 * @package store\mail\mailers
 */
class OrderMailer extends BaseNotificationMailer
{

    /**
     * @var Orders
     *
     */
    protected $_order;
    /**
     * Init options
     */
    public function init()
    {
        parent::init();

        $this->_order = ArrayHelper::getValue($this->options, 'order');

        if (empty($this->_order)) {
            throw new InvalidParamException();
        }

        /**
         * @var $suborders Suborders[]
         * @var $payment Payments
         */
        $suborders = (array)ArrayHelper::getValue($this->options, 'suborders', $this->_order->suborders);
        $payment = ArrayHelper::getValue($this->options, 'payment', $this->_order->payment);
        $options = $this->getGlobalVars();
        $checkout = Checkouts::findOne($payment->checkout_id);

        $data = [];
        $total = 0;

        /**
         * @var $suborder Suborders
         */
        foreach ($suborders as $suborder) {
            $data[] = [
                'title' => htmlspecialchars($suborder->link),
                'quantity' => $suborder->quantity,
                'price' => $suborder->amount,
            ];

            $total += $suborder->amount;
        }

        $url = null;
        if (($this->_order instanceof Orders)) {
            $url = $this->store->getSite() . '/vieworder/' . $this->_order->code;
        }

        $options['order'] = [
            'id' => $this->_order->id,
            'data' => $data,
            'sub_total' => $total,
            'total' => $total,
            'url' => $url,
            'payment_method' => $checkout ? PaymentMethods::getName($checkout->method_id) : null
        ];

        $this->html = $this->renderTwig((string)$this->template->body, $options);

        $this->subject = $this->renderTwig((string)$this->template->subject, $options);
    }
}
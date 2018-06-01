<?php
namespace common\mail\mailers\store;

use common\models\store\Orders;
use common\models\store\Payments;
use common\models\store\Suborders;
use common\models\stores\PaymentGateways;
use Yii;
use yii\base\InvalidParamException;
use yii\helpers\ArrayHelper;

/**
 * Class OrderMailer
 * @package common\mail\mailers\store
 */
class OrderMailer extends BaseNotificationMailer {

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

        $data = [];
        $total = 0;

        /**
         * @var $suborder Suborders
         */
        foreach ($suborders as $suborder) {
            $data[] = [
                'title' => $suborder->link,
                'quantity' => $suborder->quantity,
                'price' => $suborder->amount,
            ];

            $total += $suborder->amount;
        }

        $options['order'] = [
            'id' => $this->_order->id,
            'data' => $data,
            'sub_total' => $total,
            'total' => $total,
            'url' => $this->store->getSite() . '/vieworder/' . $this->_order->code,
            'payment_method' => $payment ? PaymentGateways::getMethodName($payment->method) : null
        ];

        $this->html = $this->renderTwig((string)$this->template->body, $options);

        $this->subject = $this->renderTwig((string)$this->template->subject, $options);
    }
}
<?php
namespace sommerce\mail\mailers;

use common\models\store\Orders;
use common\models\store\Payments;
use common\models\store\Suborders;
use common\models\stores\PaymentGateways;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class OrderMailer
 * @package sommerce\mail\mailers\admin
 */
class OrderMailer extends BaseNotificationMailer {

    /**
     * Init options
     */
    public function init()
    {
        parent::init();

        /**
         * @var Orders $order
         * @var $suborder Suborders
         * @var $payment Payments
         */
        $order = ArrayHelper::getValue($this->options, 'order');
        $suborders = ArrayHelper::getValue($this->options, 'suborders', $order->suborders);
        $payment = ArrayHelper::getValue($this->options, 'payment', $order->payment);
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
            'id' => $order->id,
            'data' => $data,
            'sub_total' => $total,
            'total' => $total,
            'url' => $this->store->getSite() . '/order/' . $order->id,
            'payment_method' => $payment ? PaymentGateways::getMethodName($payment->method) : null
        ];

        $this->html = $this->renderTwig((string)$this->template->body, $options);

        $this->subject = $this->renderTwig((string)$this->template->subject, $options);
    }
}
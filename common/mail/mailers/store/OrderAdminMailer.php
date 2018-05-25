<?php
namespace common\mail\mailers\store;

use common\models\store\Orders;
use common\models\store\Payments;
use common\models\store\Suborders;
use common\models\stores\PaymentGateways;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class OrderAdminMailer
 * @package common\mail\mailers\store
 */
class OrderAdminMailer extends BaseNotificationMailer {

    /**
     * Init options
     */
    public function init()
    {
        parent::init();

        /**
         * @var Orders $order
         */
        $order = ArrayHelper::getValue($this->options, 'order');
        $options = $this->getGlobalVars();

        $options['order'] = [
            'id' => $order->id,
            'url' => $this->store->getSite() . '/order/' . $order->id,
        ];

        $this->html = $this->renderTwig((string)$this->template->body, $options);

        $this->subject = $this->renderTwig((string)$this->template->subject, $options);
    }
}
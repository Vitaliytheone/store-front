<?php
namespace sommerce\mail\mailers;

use common\models\sommerce\Orders;
use yii\helpers\ArrayHelper;

/**
 * Class OrderAdminMailer
 * @package sommerce\mail\mailers
 */
class OrderAdminMailer extends BaseNotificationMailer
{
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
            'url' => $this->store->getSite() . '/admin/orders?query=' . $order->id,
        ];

        $this->html = $this->renderTwig((string)$this->template->body, $options);

        $this->subject = $this->renderTwig((string)$this->template->subject, $options);

        $this->from = null;
    }
}
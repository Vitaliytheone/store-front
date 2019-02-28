<?php
namespace sommerce\events\handlers;

use sommerce\mail\mailers\OrderMailer;
use common\models\sommerce\Suborders;
use common\models\sommerces\NotificationDefaultTemplates;
use common\models\sommerces\Stores;
use Yii;

/**
 * Class OrderCompletedEvent
 * @package sommerce\events\handlers
 */
class OrderCompletedEvent extends BaseOrderEvent {

    /**
     * @var Suborders
     */
    protected $_suborder;

    /**
     * OrderCompletedEvent constructor.
     * @param integer $storeId
     * @param integer $suborderId
     */
    public function __construct($storeId, $suborderId)
    {
        $this->_store = Stores::findOne($storeId);

        if (empty($this->_store)) {
            Yii::info('Empty ' . static::class . ' store parameter');
            return;
        }

        Yii::$app->store->setInstance($this->_store);

        if (!static::getTemplate(NotificationDefaultTemplates::CODE_ORDER_COMPLETED)) {
            return;
        }

        $this->_suborder = Suborders::findOne([
            'id' => $suborderId,
            'status' => Suborders::STATUS_COMPLETED
        ]);

        if (empty($this->_suborder)) {
            Yii::info('Empty ' . static::class . ' suborder parameter');
            return;
        }

        $this->_order = $this->_suborder->order;

    }

    /**
     * Run method
     * @return void
     */
    public function run():void
    {
        if (!$this->_suborder
            || Suborders::find()
            ->andWhere('status <> ' . Suborders::STATUS_COMPLETED)
            ->andWhere([
                'order_id' => $this->_suborder->order_id,
            ])->exists()) {
            return;
        }

        $this->customerNotify();
    }

    /**
     * Send notification to customer
     */
    protected function customerNotify()
    {
        if (!($template = static::getTemplate(NotificationDefaultTemplates::CODE_ORDER_COMPLETED))) {
            return;
        }

        if (empty($this->_order)) {
            Yii::info('Empty ' . static::class . ' order parameter');
            return;
        }

        $mailer = new OrderMailer([
            'to' => $this->_order->customer,
            'order' => $this->_order,
            'template' => $template,
            'store' => $this->_store,
        ]);
        $mailer->send();
    }
}
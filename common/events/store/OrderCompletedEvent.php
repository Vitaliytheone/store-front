<?php
namespace common\events\store;

use common\mail\mailers\store\OrderMailer;
use common\models\store\Suborders;
use common\models\stores\NotificationDefaultTemplates;
use common\models\stores\Stores;
use Yii;

/**
 * Class OrderCompletedEvent
 * @package common\events\store
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
        if (!static::getTemplate(NotificationDefaultTemplates::CODE_ORDER_COMPLETED)) {
            return;
        }

        $this->_store = Stores::findOne($storeId);

        if (empty($this->_store)) {
            Yii::error('Empty ' . static::class . ' store parameter');
            return;
        }

        Yii::$app->store->setInstance($this->_store);

        $this->_suborder = Suborders::findOne([
            'id' => $suborderId,
            'status' => Suborders::STATUS_COMPLETED
        ]);

        if (empty($this->_suborder)) {
            Yii::error('Empty ' . static::class . ' suborder parameter');
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
        if (!$this->_suborder || Suborders::find()
            ->notCompleted()
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
            Yii::error('Empty ' . static::class . ' order parameter');
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
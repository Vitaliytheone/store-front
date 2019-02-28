<?php
namespace store\events\handlers;

use common\mail\mailers\store\OrderMailer;
use common\models\store\Orders;
use common\models\store\Suborders;
use common\models\stores\NotificationDefaultTemplates;
use common\models\stores\Stores;
use Yii;

/**
 * Class OrderInProgressEvent
 * @package store\events\handlers
 */
class OrderInProgressEvent extends BaseOrderEvent {

    /**
     * @var Suborders
     */
    protected $_suborder;

    /**
     * OrderInProgressEvent constructor.
     * @param integer $storeId
     * @param integer $suborderId
     */
    public function __construct($storeId, $suborderId)
    {
        $this->_store = Stores::findOne($storeId);

        if (empty($this->_store)) {
            Yii::info('Empty ' . static::class . ' store parameter.');
            return;
        }

        Yii::$app->store->setInstance($this->_store);

        if (!static::getTemplate(NotificationDefaultTemplates::CODE_ORDER_IN_PROGRESS)) {
            return;
        }

        $this->_suborder = Suborders::findOne([
            'id' => $suborderId,
            'status' => Suborders::STATUS_IN_PROGRESS
        ]);

        if (empty($this->_suborder)) {
            Yii::info('Empty ' . static::class . ' suborder parameter.');
            return;
        }

        $this->_order = $this->_suborder->order;

        if (empty($this->_order)) {
            Yii::info('Empty ' . static::class . ' order parameter.');
            return;
        }
    }

    /**
     * Run method
     * @return void
     */
    public function run():void
    {
        if (empty($this->_order) || Orders::IN_PROGRESS_ENABLED === $this->_order->in_progress) {
            return;
        }
        
        if (!$this->_suborder || Suborders::find()->andWhere([
            'order_id' => $this->_suborder->order_id,
            'status' => Suborders::STATUS_IN_PROGRESS
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
        if (!($template = static::getTemplate(NotificationDefaultTemplates::CODE_ORDER_IN_PROGRESS))) {
            return;
        }

        $mailer = new OrderMailer([
            'to' => $this->_order->customer,
            'order' => $this->_order,
            'template' => $template,
            'store' => $this->_store,
        ]);
        $mailer->send();

        $this->_order->in_progress = Orders::IN_PROGRESS_ENABLED;
        $this->_order->save(false);
    }
}
<?php
namespace store\events\handlers;

use store\mail\mailers\OrderAdminMailer;
use common\models\store\Suborders;
use common\models\stores\NotificationDefaultTemplates;
use common\models\stores\Stores;
use Yii;

/**
 * Class OrderErrorEvent
 * @package store\events\handlers
 */
class OrderErrorEvent extends BaseOrderEvent {

    /**
     * @var Suborders
     */
    protected $_suborder;

    /**
     * OrderErrorEvent constructor.
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

        if (!static::getTemplate(NotificationDefaultTemplates::CODE_ORDER_ERROR)) {
            return;
        }

        $this->_suborder = Suborders::findOne([
            'id' => $suborderId,
            'status' => Suborders::STATUS_ERROR
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
        if (!$this->_suborder || Suborders::find()->andWhere([
            'order_id' => $this->_suborder->order_id,
            'status' => Suborders::STATUS_ERROR
        ])->exists()) {
            return;
        }

        $this->adminNotify();
    }

    /**
     * Send notification to store admins
     */
    protected function adminNotify()
    {
        if (!($template = static::getTemplate(NotificationDefaultTemplates::CODE_ORDER_ERROR))) {
            return;
        }

        if (empty($this->_order)) {
            Yii::info('Empty ' . static::class . ' order parameter');
            return;
        }

        // Берем активных админов
        $adminEmails = $this->getAdmins();

        if (empty($adminEmails)) {
            return;
        }

        foreach ($adminEmails as $adminEmail) {
            $mailer = new OrderAdminMailer([
                'to' => $adminEmail->email,
                'order' => $this->_order,
                'template' => $template,
                'store' => $this->_store,
            ]);
            $mailer->send();
        }
    }
}
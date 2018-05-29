<?php
namespace common\events\store;

use common\mail\mailers\store\OrderAdminMailer;
use common\models\store\Suborders;
use common\models\stores\NotificationDefaultTemplates;
use common\models\stores\Stores;
use Yii;

/**
 * Class OrderErrorEvent
 * @package common\events\store
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
        if (!static::getTemplate(NotificationDefaultTemplates::CODE_ORDER_ERROR)) {
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
            'status' => Suborders::STATUS_ERROR
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
        if (!$this->_suborder || Suborders::find()->andWhere([
            'order_id' => $this->_suborder->order_id,
            'status' => Suborders::STATUS_ERROR
        ])->andWhere('id <> ' . $this->_suborder->id)->exists()) {
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
            Yii::error('Empty ' . static::class . ' order parameter');
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
<?php

namespace frontend\modules\admin\models\forms;

use common\models\store\Suborders;
use common\models\stores\Stores;
use common\models\stores\StoresSendOrders;

/**
 * Class StatusForm
 * @package frontend\modules\admin\models\forms
 */
class SuborderForm extends Suborders
{
    /** @var  Stores */
    private $_store;

    /* Current statuses when `Change status` action is disallowed */
    public static $disallowedChangeStatusStatuses = [
        self::STATUS_AWAITING,
        self::STATUS_CANCELED,
        self::STATUS_COMPLETED,
    ];

    /* Accepted statuses for changes from admin panel */
    public static $acceptedStatuses = [
        self::STATUS_PENDING,
        self::STATUS_IN_PROGRESS,
        self::STATUS_COMPLETED,
    ];

    /* Current statuses when `Cancel` action is disallowed */
    public static $disallowedCancelStatuses = [
        Suborders::STATUS_AWAITING,
        Suborders::STATUS_CANCELED,
        Suborders::STATUS_COMPLETED,
    ];

    /**
     * Set Store
     * @param Stores $store
     */
    public function setStore(Stores $store)
    {
        $this->_store = $store;
    }

    /**
     * Change suborder status
     * @param $status
     * @param int $mode
     * @return bool
     */
    public function changeStatus($status, $mode = self::MODE_MANUAL)
    {

        // Check if model can accept $status
        $currentStatus = $this->getAttribute('status');
        if (
            in_array($currentStatus, static::$disallowedChangeStatusStatuses) ||
            !in_array($status, static::$acceptedStatuses)
        ) {
            return false;
        }

        return parent::changeStatus($status);
    }

    /**
     * Cancel suborder
     * @return bool
     */
    public function cancel()
    {
        // Check if model ready for cancel
        $currentStatus = $this->getAttribute('status');
        if (in_array($currentStatus, static::$disallowedCancelStatuses)) {
            return false;
        }

        return parent::cancel();
    }

    /**
     * Resend suborder
     * @return bool
     */
    public function resend()
    {
        $currentStatus = $this->getAttribute('status');
        if ($currentStatus !== self::STATUS_FAILED) {
            return false;
        }

        // Make queue for sender
        if (Suborders::MODE_AUTO == $this->mode) {

            $sendOrderExist = StoresSendOrders::findOne([
                'store_id' => $this->_store->id,
                'suborder_id' => $this->id,
            ]);

            if (!$sendOrderExist) {
                $sendOrder = new StoresSendOrders();
                $sendOrder->store_id = $this->_store->id;
                $sendOrder->store_db = $this->_store->db_name;
                $sendOrder->provider_id = $this->provider_id;
                $sendOrder->suborder_id = $this->id;
                $sendOrder->save(false);
            }
        }

        return parent::resend();
    }

}
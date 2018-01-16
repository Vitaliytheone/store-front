<?php

namespace frontend\modules\admin\models\forms;

use common\models\store\Suborders;

/**
 * Class StatusForm
 * @package frontend\modules\admin\models\forms
 */
class SuborderForm extends Suborders
{
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

        return parent::resend();
    }

}
<?php

namespace frontend\modules\admin\models\forms;

use common\models\store\Suborders;

/**
 * Class SubordersListForm
 * @package frontend\modules\admin\models\forms
 */
class SubordersListForm extends Suborders
{
    /* Suborder accepted statuses for changes from admin panel */
    public static $acceptedStatuses = [
        Suborders::STATUS_PENDING,
        Suborders::STATUS_IN_PROGRESS,
        Suborders::STATUS_COMPLETED,
    ];

    /* Suborder statuses when `Change status` action is disallowed */
    public static $disallowedChangeStatusStatuses = [
        Suborders::STATUS_AWAITING,
        Suborders::STATUS_CANCELED,
        Suborders::STATUS_COMPLETED,
    ];

    /* Suborder statuses when `Cancel suborder` action is disallowed */
    public static $disallowedCancelStatuses = [
        Suborders::STATUS_AWAITING,
        Suborders::STATUS_CANCELED,
        Suborders::STATUS_COMPLETED,
    ];

    /* Suborder statuses when `View details` action disallowed */
    public static $disallowedDetailsStatuses = [
        Suborders::STATUS_AWAITING,
        Suborders::STATUS_CANCELED,
    ];

    /**
     * Return action menu or null
     * @return array|null
     */
    public function getActionMenu()
    {
        // Create `change status` menu
        $changeStatus = false;

        if (!in_array($this->status, static::$disallowedChangeStatusStatuses)) {
            foreach (static::$acceptedStatuses as $acceptedStatus) {
                if ($this->status == $acceptedStatus) {
                    continue;
                }
                $changeStatus[] = [
                    'status' => $acceptedStatus,
                    'status_title' => static::getStatusName($acceptedStatus),
                ];
            }
        }

        // `details` menu
        $details = ($this->mode === self::MODE_AUTO) && !in_array($this->status, static::$disallowedDetailsStatuses);

        // `resend` menu
        $resend = $this->status === self::STATUS_FAILED;

        // `cancel`
        $cancel = !in_array($this->status, static::$disallowedCancelStatuses);

        $actionMenu = ($details || $resend || $changeStatus || $cancel) ? [
            'details' => $details,
            'resend' => $resend,
            'status' => $changeStatus,
            'cancel' => $cancel,
        ] : null;

        return $actionMenu;
    }

}
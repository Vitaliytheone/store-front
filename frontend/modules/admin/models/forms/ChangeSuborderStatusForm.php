<?php

namespace frontend\modules\admin\models\forms;

use common\models\store\Suborders;

/**
 * Class ChangeSuborderStatusForm
 * @package frontend\modules\admin\models\forms
 */
class ChangeSuborderStatusForm extends Suborders
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

    /**
     * @inheritdoc
     */
    public function rules()
    {   return [
            ['status', 'in', 'range' => self::$acceptedStatuses],
            ['mode', 'safe'],
        ];
    }

    /**
     * Change suborder status if allowed
     * @param $status
     * @return array|mixed
     */
    public function changeStatus($status)
    {
        // Check if model ready for changes
        $currentStatus = $this->getAttribute('status');
        if (in_array($currentStatus, static::$disallowedChangeStatusStatuses)) {
            return false;
        }

        $this->setAttributes([
            'status' => $status,
            'mode' => self::MODE_MANUAL,
        ]);

        if (!$this->save()) {
            return false;
        }

        return $this->getAttribute('status');
    }
}
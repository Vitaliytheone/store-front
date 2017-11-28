<?php

namespace frontend\modules\admin\models\forms;

use common\models\store\Suborders;

/**
 * Class CancelSuborderForm
 * @package frontend\modules\admin\models\forms
 */
class CancelSuborderForm extends Suborders
{
    /* Current statuses when `Cancel` action is disallowed */
    public static $disallowedCancelStatuses = [
        Suborders::STATUS_AWAITING,
        Suborders::STATUS_CANCELED,
        Suborders::STATUS_COMPLETED,
    ];

    /**
     * @inheritdoc
     */
    public function rules()
    {   return [
            ['status', 'safe'],
        ];
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

        $this->setAttribute('status', self::STATUS_CANCELED);
        if (!$this->save()) {
            return false;
        };

        return true;
    }
}
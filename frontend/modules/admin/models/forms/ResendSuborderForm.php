<?php

namespace frontend\modules\admin\models\forms;

use common\models\store\Suborders;

/**
 * Class ResendSuborderForm
 * @package frontend\modules\admin\models\forms
 */
class ResendSuborderForm extends Suborders
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mode', 'status', 'send'], 'safe'],
        ];
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

        $this->setAttributes([
            'status' => self::STATUS_AWAITING,
            'send' => self::RESEND_NO,
        ]);

        if (!$this->save()) {
           return false;
        };

        return true;
    }
}
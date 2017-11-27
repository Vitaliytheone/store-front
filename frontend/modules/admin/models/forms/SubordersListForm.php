<?php

namespace frontend\modules\admin\models\forms;

use yii;
use yii\db\Query;
use common\models\store\Suborders;

/**
 * Class SubordersListForm
 * @package frontend\modules\admin\models\forms
 */
class SubordersListForm extends Suborders
{

    const SCENARIO_CHANGE_STATUS_ACTION = 'change_status_action';
    const SCENARIO_CHANGE_STATUS_ACTION_ATTR = 'change_status_action_attr';

    const SCENARIO_CANCEL_ACTION = 'allowed_cancel';
    const SCENARIO_RESEND_ACTION = 'allowed_resend';
    const SCENARIO_DETAILS_ACTION = 'allowed_details';

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
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['status', 'in', 'not' => true, 'range' => self::$disallowedChangeStatusStatuses,
                'on' => self::SCENARIO_CHANGE_STATUS_ACTION],

            ['status', 'in', 'range' => self::$acceptedStatuses,
                'on' => self::SCENARIO_CHANGE_STATUS_ACTION_ATTR],

            ['mode', 'safe',
                'on' => self::SCENARIO_CHANGE_STATUS_ACTION_ATTR],

            ['status', 'in', 'not' => true, 'range' => self::$disallowedCancelStatuses,
                'on' => self::SCENARIO_CANCEL_ACTION],

            ['status', 'compare', 'compareValue' => Suborders::STATUS_FAILED, 'operator' => '===', 'type' => 'number',
                'on' => self::SCENARIO_RESEND_ACTION],

            ['mode', 'compare', 'compareValue' => Suborders::MODE_AUTO, 'operator' => '===', 'type' => 'number',
                'on' => self::SCENARIO_DETAILS_ACTION],
            ['status', 'in', 'not' => true, 'range' => static::$disallowedDetailsStatuses,
                'on' => self::SCENARIO_DETAILS_ACTION]
        ];
    }



    /**
     * Return Suborder Details data
     * Defines is returned provider response
     * plain Json string or print_r formatted string
     * @param bool $responseFormatPrintR
     * @return array|null
     */
    public function getDetails(bool $responseFormatPrintR = true)
    {
        $provider = (new Query())
            ->select(['site'])
            ->from("providers")
            ->where(['id' => $this->provider_id])
            ->one();
        if (!$provider) {
            return null;
        }
        $providerResponse = $this->provider_response;
        if ($responseFormatPrintR) {
            $providerResponse = print_r(json_decode($providerResponse), 1);
        }
        $formatter = Yii::$app->formatter;
        $orderDetails = [
            'provider' => $provider['site'],
            'provider_order_id' => $this->provider_order_id,
            'provider_response' => $providerResponse,
            'updated_at' => $formatter->asDatetime($this->updated_at,'yyyy-MM-dd HH:mm:ss'),
        ];

        return $orderDetails;
    }

    /**
     * Return action menu or null
     * @return array|null
     */
    public function getActionMenu()
    {
        // Create `change status` menu
        $changeStatus = false;

        $this->setScenario(self::SCENARIO_CHANGE_STATUS_ACTION);
        if ($this->validate()) {
            foreach (static::$acceptedStatuses as $acceptedStatus) {
                if ($this->status == $acceptedStatus) {
                    continue;
                }

                $changeStatus[] = [
                    'status' => $acceptedStatus,
                    'status_title' => static::getStatusTitle($acceptedStatus),
                ];
            }
        }

        // `details` menu
        $this->setScenario(static::SCENARIO_DETAILS_ACTION);
        $details = $this->validate();

        // `resend` menu
        $this->setScenario(static::SCENARIO_RESEND_ACTION);
        $resend = $this->validate();

        // `cancel`
        $this->setScenario(static::SCENARIO_CANCEL_ACTION);
        $cancel = $this->validate();

        $actionMenu = ($details || $resend || $changeStatus || $cancel) ? [
            'details' => $details,
            'resend' => $resend,
            'status' => $changeStatus,
            'cancel' => $cancel,
        ] : null;

        return $actionMenu;
    }

}
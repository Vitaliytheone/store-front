<?php

namespace frontend\modules\admin\models\forms;

use yii;
use yii\behaviors\AttributeBehavior;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use frontend\modules\admin\models\search\OrdersSearch;

/**
 * Class SubordersListForm
 * @package frontend\modules\admin\models\forms
 */
class SubordersListForm extends \common\models\store\Suborders
{

    const SCENARIO_CHANGE_STATUS_ACTION = 'change_status_action';
    const SCENARIO_CHANGE_STATUS_ACTION_ATTR = 'change_status_action_attr';

    const SCENARIO_CANCEL_ACTION = 'allowed_cancel';
    const SCENARIO_RESEND_ACTION = 'allowed_resend';

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['status', 'in', 'not' => true, 'range' => OrdersSearch::$disallowedChangeStatusStatuses,
                'on' => self::SCENARIO_CHANGE_STATUS_ACTION],

            ['status', 'in', 'range' => OrdersSearch::$acceptedStatuses,
                'on' => self::SCENARIO_CHANGE_STATUS_ACTION_ATTR],
            ['mode', 'safe',
                'on' => self::SCENARIO_CHANGE_STATUS_ACTION_ATTR],


            ['status', 'in', 'not' => true, 'range' => OrdersSearch::$disallowedCancelStatuses,
                'on' => self::SCENARIO_CANCEL_ACTION],

            ['status', 'compare', 'compareValue' => self::STATUS_FAILED, 'operator' => '===', 'type' => 'number',
                'on' => self::SCENARIO_RESEND_ACTION],
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
}
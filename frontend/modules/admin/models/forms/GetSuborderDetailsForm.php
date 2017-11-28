<?php

namespace frontend\modules\admin\models\forms;

use Yii;
use yii\db\Query;
use common\models\store\Suborders;

/**
 * Class GetSuborderDetailsForm
 * @package frontend\modules\admin\models\forms
 */
class GetSuborderDetailsForm extends Suborders
{
    /**
     * Return Suborder Details data
     * Defines is returned provider response
     * plain Json string or print_r formatted string
     * @param bool $responseFormatPrintR
     * @return array|null
     */
    public function details(bool $responseFormatPrintR = true)
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
<?php

namespace frontend\modules\admin\models\forms;

use yii;
use yii\behaviors\AttributeBehavior;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * Class SubordersListForm
 * @package frontend\modules\admin\models\forms
 */
class SubordersListForm extends \common\models\store\Suborders
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            /** Change `mode` to manual when `status` changes from admin panel */
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => 'mode',
                    self::EVENT_BEFORE_UPDATE => 'mode',
                ],
                'value' => function ($event) {
                    $isStatusChanged = $this->isAttributeChanged('status');
                    return $isStatusChanged ? self::MODE_MANUAL : $this->mode;
                },
            ],
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
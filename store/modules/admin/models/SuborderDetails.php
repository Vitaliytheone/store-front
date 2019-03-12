<?php

namespace store\modules\admin\models;

use Yii;
use common\models\store\Suborders;
use common\helpers\CustomHtmlHelper;

/**
 * Class Suborder
 * @package store\modules\admin\models
 */
class SuborderDetails extends Suborders
{
    /**
     * Return Suborder Details data
     * @return array|null
     * @throws \yii\base\InvalidConfigException
     */
    public function details()
    {
        $provider = $this->provider;

        if (!$provider) {
            return null;
        }

        $formatter = Yii::$app->formatter;
        $orderDetails = [
            'provider' => $provider->site,
            'provider_order_id' => $this->provider_order_id,
            'provider_response' => CustomHtmlHelper::responseFormatter($this->provider_response),
            'updated_at' => $formatter->asDatetime($this->updated_at,'yyyy-MM-dd HH:mm:ss'),
        ];

        return $orderDetails;
    }
}
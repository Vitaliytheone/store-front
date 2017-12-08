<?php

namespace frontend\modules\admin\models;

use common\models\stores\Providers;
use Yii;
use yii\db\Query;
use common\models\store\Suborders;

/**
 * Class Suborder
 * @package frontend\modules\admin\models
 */
class SuborderDetails extends Suborders
{
    /**
     * Return Suborder Details data
     * @return array|null
     */
    public function details()
    {
        $providersTable = Providers::tableName();

        $provider = (new Query())
            ->select(['site'])
            ->from($providersTable)
            ->where(['id' => $this->provider_id])
            ->one();
        if (!$provider) {
            return null;
        }

        $providerResponse = print_r(json_decode($this->provider_response), 1);

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
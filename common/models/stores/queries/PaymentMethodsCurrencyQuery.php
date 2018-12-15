<?php

namespace common\models\stores\queries;


use yii\db\ActiveQuery;
use common\models\stores\PaymentMethodsCurrency;

/**
 * Class PaymentMethodsCurrencyQuery
 * @package common\models\stores\queries
 */
class PaymentMethodsCurrencyQuery extends ActiveQuery
{
    /**
     * {@inheritdoc}
     * @return PaymentMethodsCurrency[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return PaymentMethodsCurrency|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
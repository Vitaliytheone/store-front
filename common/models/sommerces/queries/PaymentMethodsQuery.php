<?php

namespace common\models\sommerces\queries;


use Yii;

/**
 * This is the ActiveQuery class for [[\common\models\sommerces\PaymentMethods]].
 *
 * @see \common\models\sommerces\PaymentMethods
 */
class PaymentMethodsQuery extends \yii\db\ActiveQuery
{
    /**
     * @inheritdoc
     * @return \common\models\sommerces\PaymentMethods[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\sommerces\PaymentMethods|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}

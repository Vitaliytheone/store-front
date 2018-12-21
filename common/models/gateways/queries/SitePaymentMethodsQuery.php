<?php

namespace common\models\gateways\queries;

use yii\db\ActiveQuery;
use common\models\gateways\SitePaymentMethods;

/**
 * This is the ActiveQuery class for [[SitePaymentMethods]].
 *
 * @see SitePaymentMethods
 */
class SitePaymentMethodsQuery extends ActiveQuery
{
    public function active()
    {
        return $this->andWhere([
            'visibility' => SitePaymentMethods::VISIBILITY_ENABLED
        ]);
    }

    /**
     * @inheritdoc
     * @return SitePaymentMethods[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return SitePaymentMethods|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}

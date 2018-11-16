<?php

namespace common\models\panels\queries;

use yii\db\ActiveQuery;
use common\models\panels\PanelPaymentMethods;

/**
 * This is the ActiveQuery class for [[PanelPaymentMethods]].
 *
 * @see PanelPaymentMethods
 */
class PanelPaymentMethodsQuery extends ActiveQuery
{
    public function active()
    {
        return $this->andWhere([
            'visibility' => 1
        ]);
    }

    /**
     * @inheritdoc
     * @return PanelPaymentMethods[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return PanelPaymentMethods|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
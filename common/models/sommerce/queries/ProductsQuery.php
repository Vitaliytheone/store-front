<?php

namespace common\models\sommerce\queries;

use yii\db\ActiveQuery;
use common\models\sommerce\Products;

/**
 * This is the ActiveQuery class for [[Products]].
 *
 * @see Products
 */
class ProductsQuery extends ActiveQuery
{
    public function active()
    {
        return $this->andWhere([
            'visibility' => Products::VISIBILITY_YES
        ]);
    }

    /**
     * @inheritdoc
     * @return Products[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Products|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}

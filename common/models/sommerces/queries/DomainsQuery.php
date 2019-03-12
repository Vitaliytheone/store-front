<?php

namespace common\models\sommerces\queries;

use common\models\sommerces\Domains;

/**
 * This is the ActiveQuery class for [[\common\models\sommerces\Domains]].
 *
 * @see \common\models\sommerces\Domains
 */
class DomainsQuery extends \yii\db\ActiveQuery
{
    /**
     * @return DomainsQuery
     */
    public function active()
    {
        return $this->andWhere(['status' => Domains::STATUS_OK]);
    }

    /**
     * @inheritdoc
     * @return \common\models\sommerces\Domains[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\sommerces\Domains|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}

<?php

namespace common\models\panels\queries;

use common\models\panels\Domains;

/**
 * This is the ActiveQuery class for [[\common\models\panels\Domains]].
 *
 * @see \common\models\panels\Domains
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
     * @return \common\models\panels\Domains[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\panels\Domains|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}

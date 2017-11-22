<?php

namespace common\models\store\queries;

use yii\db\ActiveQuery;
use common\models\store\Packages;

/**
 * This is the ActiveQuery class for [[\common\models\store\Packages]].
 *
 * @see \common\models\store\Packages
 */
class PackagesQuery extends ActiveQuery
{
    public function active()
    {
        return $this->andWhere([
            'deleted' => Packages::DELETED_NO,
            'visibility' => Packages::VISIBILITY_YES
        ]);
    }

    /**
     * @inheritdoc
     * @return \common\models\store\Packages[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\store\Packages|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}

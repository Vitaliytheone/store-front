<?php

namespace common\models\sommerce\queries;

use yii\db\ActiveQuery;
use common\models\sommerce\Packages;

/**
 * This is the ActiveQuery class for [[\common\models\sommerce\Packages]].
 *
 * @see \common\models\sommerce\Packages
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
     * @return \common\models\sommerce\Packages[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\sommerce\Packages|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}

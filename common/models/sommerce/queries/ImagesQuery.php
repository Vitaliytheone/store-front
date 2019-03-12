<?php

namespace common\models\sommerce\queries;

use yii\db\ActiveQuery;
use common\models\sommerce\Images;

/**
 * This is the ActiveQuery class for [[\common\models\sommerce\Images]].
 *
 * @see Images
 */
class ImagesQuery extends ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return Images[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Images|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}

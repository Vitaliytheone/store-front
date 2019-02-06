<?php

namespace common\models\gateway\queries;

use yii\db\ActiveQuery;
use common\models\gateway\Files;

/**
 * This is the ActiveQuery class for [[ThemesFiles]].
 *
 * @see ThemesFiles
 */
class FilesQuery extends ActiveQuery
{
    public function active()
    {
        return $this->andWhere([
            'is_deleted' => 0
        ]);
    }

    public function page()
    {
        return $this->andWhere([
            'file_type' => Files::FILE_TYPE_PAGE
        ]);
    }

    public function snippet()
    {
        return $this->andWhere([
            'file_type' => Files::FILE_TYPE_SNIPPET
        ]);
    }

    public function layout()
    {
        return $this->andWhere([
            'file_type' => Files::FILE_TYPE_LAYOUT
        ]);
    }

    /**
     * @inheritdoc
     * @return Files[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Files|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}

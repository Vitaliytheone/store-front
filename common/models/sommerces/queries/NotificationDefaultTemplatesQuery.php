<?php
namespace common\models\sommerces\queries;

use yii\db\ActiveQuery;
use common\models\sommerces\NotificationDefaultTemplates;

/**
 * This is the ActiveQuery class for [[NotificationDefaultTemplates]].
 *
 * @see NotificationDefaultTemplates
 */
class NotificationDefaultTemplatesQuery extends ActiveQuery
{
    public function active()
    {
        return $this->andWhere([
            'status' => NotificationDefaultTemplates::STATUS_ENABLED
        ]);
    }

    /**
     * @inheritdoc
     * @return NotificationDefaultTemplates[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return NotificationDefaultTemplates|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
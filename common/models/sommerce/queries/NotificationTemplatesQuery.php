<?php
namespace common\models\sommerce\queries;

use yii\db\ActiveQuery;
use common\models\sommerce\NotificationTemplates;

/**
 * This is the ActiveQuery class for [[NotificationTemplates]].
 *
 * @see \common\models\sommerce\NotificationTemplates
 */
class NotificationTemplatesQuery extends ActiveQuery
{
    public function active()
    {
        return $this->andWhere([
            'status' => NotificationTemplates::STATUS_ENABLED
        ])->andWhere('body IS NOT NULL');
    }

    /**
     * @inheritdoc
     * @return NotificationTemplates[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return NotificationTemplates|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
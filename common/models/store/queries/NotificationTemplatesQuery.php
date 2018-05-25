<?php
namespace common\models\store\queries;

use yii\db\ActiveQuery;
use common\models\store\NotificationTemplates;

/**
 * This is the ActiveQuery class for [[NotificationTemplates]].
 *
 * @see \common\models\store\NotificationTemplates
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
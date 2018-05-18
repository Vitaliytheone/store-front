<?php
namespace common\models\store\queries;

use yii\db\ActiveQuery;
use common\models\store\NotificationAdminEmails;

/**
 * This is the ActiveQuery class for [[NotificationAdminEmails]].
 *
 * @see NotificationAdminEmails
 */
class NotificationAdminEmailsQuery extends ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return NotificationAdminEmails[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return NotificationAdminEmails|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
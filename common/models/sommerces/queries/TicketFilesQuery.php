<?php

namespace common\models\sommerces\queries;

/**
 * This is the ActiveQuery class for [[\common\models\sommerces\TicketFiles]].
 *
 * @see \common\models\sommerces\TicketFiles
 */
class TicketFilesQuery extends \yii\db\ActiveQuery
{
    public function ticketView($ticketId)
    {
        return $this->andWhere([
            'ticket_id' => $ticketId,
        ])
            ->joinWith(['customer', 'admin'])
            ->orderBy(['created_at' => SORT_ASC]);
    }

    /**
     * @inheritdoc
     * @return \common\models\sommerces\TicketFiles[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\sommerces\TicketFiles|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
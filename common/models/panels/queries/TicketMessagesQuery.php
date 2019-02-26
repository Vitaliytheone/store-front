<?php

namespace common\models\panels\queries;

/**
 * This is the ActiveQuery class for [[\common\models\panels\TicketMessages]].
 *
 * @see \common\models\panels\TicketMessages
 */
class TicketMessagesQuery extends \yii\db\ActiveQuery
{
    public function ticketView($ticketId)
    {
        return $this->andWhere([
            'ticket_messages.ticket_id' => $ticketId,
            'is_system' => 0
        ])
            ->joinWith(['customer', 'admin', 'file'])
            ->orderBy(['created_at' => SORT_ASC]);
    }

    /**
     * @inheritdoc
     * @return \common\models\panels\TicketMessages[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\panels\TicketMessages|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
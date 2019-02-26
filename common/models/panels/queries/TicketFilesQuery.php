<?php

namespace common\models\panels\queries;

/**
 * This is the ActiveQuery class for [[\common\models\panels\TicketFiles]].
 *
 * @see \common\models\panels\TicketFiles
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
     * @return \common\models\panels\TicketFiles[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\panels\TicketFiles|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
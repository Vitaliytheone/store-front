<?php

namespace my\modules\superadmin\models\forms;


use common\models\panels\TicketNotes;
use yii\base\Model;

/**
 * Class TicketNoteForm
 * @package my\modules\superadmin\models\forms
 */
class TicketNoteForm extends Model
{
    public $note;

    public const SCENARIO_CREATE = 'create';
    public const SCENARIO_EDIT = 'edit';

    protected $_customerId;

    public function rules()
    {
        return [
            ['note', 'string'],
            [['note'], 'trim'],
        ];
    }

    /**
     * Set ticket id
     * @param int $customerId
     */
    public function setCustomerId($customerId)
    {
        $this->_customerId = $customerId;
    }

    /**
     * @return bool
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function save(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        switch ($this->scenario) {
            case self::SCENARIO_CREATE:
                $ticketNote = new TicketNotes();
                $ticketNote->note = $this->note;
                $ticketNote->customer_id = $this->_customerId;

                if (!$ticketNote->save()) {
                    return false;
                }
                break;

            case self::SCENARIO_EDIT:
                $ticketNote = TicketNotes::findOne(['customer_id' => $this->_customerId]);

                if ($this->note == '') {
                     if (!$ticketNote->delete()) {
                         return false;
                     }
                } else {
                    $ticketNote->note = $this->note;
                    if (!$ticketNote->save()) {
                        return false;
                    }
                }
                break;
        }

        return true;
    }
}
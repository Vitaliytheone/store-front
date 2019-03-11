<?php

namespace superadmin\models\forms;


use common\models\sommerces\CustomersNote;
use yii\base\Model;

/**
 * Class TicketNoteForm
 * @package superadmin\models\forms
 */
class TicketNoteForm extends Model
{
    public $note;

    public const SCENARIO_CREATE = 'create';
    public const SCENARIO_EDIT = 'edit';

    protected $_customerId;

    /**
     * @var CustomersNote
     */
    protected $_ticketNote;

    public function rules()
    {
        return [
            ['note', 'string'],
            [['note'], 'required', 'on' => self::SCENARIO_CREATE],
            [['note'], 'trim'],
        ];
    }

    public function scenarios()
    {
        return [
            self::SCENARIO_CREATE => ['note'],
            self::SCENARIO_EDIT => ['note'],
        ];
    }

    /**
     * Set ticket id
     * @param int $customerId
     */
    public function setCustomerId(int $customerId)
    {
        $this->_customerId = $customerId;
    }

    /**
     * @param $note
     */
    public function setNote(CustomersNote $note)
    {
        $this->_ticketNote = $note;
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
                $ticketNote = new CustomersNote();
                $ticketNote->note = $this->note;
                $ticketNote->customer_id = $this->_customerId;

                if (!$ticketNote->save()) {
                    return false;
                }
                break;

            case self::SCENARIO_EDIT:
                if ($this->note == '') {
                     if (!$this->_ticketNote->delete()) {
                         return false;
                     }
                } else {
                    $this->_ticketNote->note = $this->note;
                    if (!$this->_ticketNote->save()) {
                        return false;
                    }
                }
                break;
        }

        return true;
    }
}
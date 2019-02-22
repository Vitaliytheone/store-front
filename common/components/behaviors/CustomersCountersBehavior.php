<?php

namespace common\components\behaviors;

use common\components\ActiveForm;
use common\models\panels\CustomersCounters;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\db\Exception;

/**
 * Class CustomersCountersBehavior
 * @package common\components\behaviors
 */
class CustomersCountersBehavior extends Behavior
{
    /** @var string */
    public $column;

    /** @var integer */
    public $customerId;

    /**
     * @return array
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'afterInsert',
            ActiveRecord::EVENT_AFTER_DELETE => 'afterDelete',
        ];
    }

    /**
     * @param $event
     * @throws Exception
     */
    public function afterInsert($event)
    {
        $count = $this->getCounter();
        $customerId = $this->getCustomerId();
        $column = $this->getColumnName();

        if (!isset($column)) {
            throw new Exception('column does not exist');
        }

        if (!isset($count)) {
            $count = new CustomersCounters();
            $count->customer_id = $customerId;
            $count->$column = 0;
        }

        $count->$column += 1;
        if (!$count->save(false)) {
            throw new Exception(ActiveForm::firstError($count));
        }
    }

    /**
     * @param $event
     * @throws Exception
     */
    public function afterDelete($event)
    {
        $count = $this->getCounter();

        if (isset($count)) {
            $count->{$this->column} -= 1;

            if (!$count->save(false)) {
                throw new Exception(ActiveForm::firstError($count));
            }
        }
    }

    /**
     * @return CustomersCounters|null
     * @throws Exception
     */
    private function getCounter(): ?CustomersCounters
    {
        $customerId = $this->getCustomerId();
        if (!isset($customerId)) {
            throw new Exception('customer_id does not exist');
        }

        return CustomersCounters::findOne(['customer_id' => $customerId]);
    }

    /**
     * @return int|null
     */
    private function getCustomerId(): ?int
    {
        if ($this->customerId instanceof \Closure || (is_array($this->customerId) && is_callable($this->customerId))) {
            return call_user_func($this->customerId);
        }

        return $this->customerId;
    }

    /**
     * @return string|null
     */
    private function getColumnName(): ?string
    {
        if ($this->column instanceof \Closure || (is_array($this->column) && is_callable($this->column))) {
            return call_user_func($this->column);
        }

        return $this->column;
    }
}

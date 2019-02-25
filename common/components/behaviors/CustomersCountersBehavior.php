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

        $count->$column = $this->getCurrentCounter($customerId);
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
        $counter = $this->getCounter();
        $customerId = $this->getCustomerId();

        if (isset($counter)) {
            $counter->{$this->column} = $this->getCurrentCounter($customerId);

            if (!$counter->save(false)) {
                throw new Exception(ActiveForm::firstError($counter));
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

        return CustomersCounters::findOne(['customer_id' => $customerId]);
    }

    /**
     * @return int|null
     * @throws Exception
     */
    private function getCustomerId(): ?int
    {
        if (!isset($this->customerId)) {
            throw new Exception('customer_id does not exist');
        }

        return $this->owner->{$this->customerId};
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

    /**
     * @param int $customerId
     * @return int|string
     */
    private function getCurrentCounter(int $customerId)
    {
        $column = $this->getColumnName();
        $modelName = get_class($this->owner);
        /** @var $modelName ActiveRecord */
        $model = $modelName::find()->andWhere([$this->customerId => $customerId]);

        if ($column === 'panels') {
            $model->andWhere(['child_panel' => 0]);
        } elseif ($column === 'child_panels') {
            $model->andWhere(['child_panel' => 1]);
        }

        return $model->count();
    }
}

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

        if (!isset($count)) {
            $count = new CustomersCounters();
            $count->customer_id = $this->customerId;
            $count->{$this->column} = 0;
        }

        $count->{$this->column} += 1;
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
     */
    private function getCounter(): ?CustomersCounters
    {
        return CustomersCounters::findOne(['customer_id' => $this->customerId]);
    }
}

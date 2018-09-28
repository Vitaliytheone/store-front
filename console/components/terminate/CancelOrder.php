<?php

namespace console\components\terminate;

use common\models\panels\Orders;
use Yii;

/**
 * Class TerminateOnePanel
 * @package console\components\terminate
 */
class CancelOrder
{
    /**
     * @var integer
     */
    protected $_date;

    /**
     * CancelOrder constructor.
     * @param integer $date
     */
    public function __construct($date)
    {
        $this->_date = $date;
    }

    public function run()
    {
        /**
         * @var $order Orders
         */
        foreach (Orders::find()->andWhere('status = :pending AND date < :date', [
            ':pending' => Orders::STATUS_PENDING,
            ':date' => $this->_date
        ])->all() as $order) {
            $order->cancel();
        }
    }
}
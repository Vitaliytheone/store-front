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
     * @var integer|array
     */
    protected $_item;

    /**
     * CancelOrder constructor.
     * @param integer $date
     * @param integer|array $item
     */
    public function __construct($date, $item = null)
    {
        $this->_date = $date;
        $this->_item = $item;
    }

    public function run()
    {
        $query = Orders::find()->andWhere('status = :pending AND date < :date', [
            ':pending' => Orders::STATUS_PENDING,
            ':date' => $this->_date
        ]);

        if ($this->_item) {
            $query->andWhere([
                'item' => $this->_item
            ]);
        }

        /**
         * @var $order Orders
         */
        foreach ($query->all() as $order) {
            $order->cancel();
        }
    }
}
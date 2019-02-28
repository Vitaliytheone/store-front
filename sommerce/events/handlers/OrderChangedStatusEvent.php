<?php
namespace sommerce\events\handlers;

use sommerce\events\Events;
use common\models\sommerce\Suborders;
use Yii;

/**
 * Class OrderChangedStatusEvent
 * @package sommerce\events\handlers
 */
class OrderChangedStatusEvent extends BaseOrderEvent {

    /**
     * @var integer
     */
    protected $_suborderId;

    /**
     * @var integer
     */
    protected $_storeId;

    /**
     * @var integer
     */
    protected $_status;

    /**
     * OrderChangedStatusEvent constructor.
     * @param integer $storeId
     * @param integer $suborderId
     * @param integer $status
     */
    public function __construct($storeId, $suborderId, $status)
    {
        $this->_storeId = (int)$storeId;
        $this->_suborderId = (int)$suborderId;
        $this->_status = (int)$status;
    }

    /**
     * Run method
     * @return void
     */
    public function run():void
    {
        $data = [
            'suborderId' => $this->_suborderId,
            'storeId' => $this->_storeId,
        ];

        switch ($this->_status) {
            case Suborders::STATUS_IN_PROGRESS:
                // Event in progress order
                Events::add(Events::EVENT_SOMMERCE_ORDER_IN_PROGRESS, $data);
            break;

            case Suborders::STATUS_COMPLETED:
                // Event completed order
                Events::add(Events::EVENT_SOMMERCE_ORDER_COMPLETED, $data);
            break;

            case Suborders::STATUS_ERROR:
                // Event error order
                Events::add(Events::EVENT_SOMMERCE_ORDER_ERROR, $data);
            break;

            case Suborders::STATUS_FAILED:
                // Event fail order
                Events::add(Events::EVENT_SOMMERCE_ORDER_FAIL, $data);
            break;
        }
    }
}
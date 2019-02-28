<?php
namespace control_panel\widgets;

use yii\base\InvalidParamException;
use yii\base\Widget;
use yii\data\Pagination;

/**
 * Class PagesInfoWidget
 * @package control_panel\widgets
 */
class PagesInfoWidget extends Widget {

    /**
     * @var Pagination
     */
    public $pagination;

    /**
     * Run method
     * @return string|void
     */
    public function run()
    {
        if (!($this->pagination instanceof Pagination)) {
            throw new InvalidParamException();
        }

        $from = $this->pagination->offset + 1;

        $total = $this->pagination->totalCount;

        $to = $this->pagination->offset + $this->pagination->limit;

        if ($total < $to) {
            $to = $total;
        }

        if (!$total) {
            return;
        }

        echo $from . ' to ' . $to . ' of ' . $total;
    }
}
<?php

namespace store\widgets;

use yii\base\Widget;

/**
 * Class AnalyticsWidget
 * @package store\modules\admin\widgets
 */
class AnalyticsWidget extends Widget
{
    /** @var string */
    public $content;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
    }

    /**
     * @return string|void
     */
    public function run()
    {
        return $this->content;
    }
}

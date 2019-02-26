<?php

namespace sommerce\widgets;

use yii\base\Widget;

/**
 * Class AnalyticsWidget
 * @package sommerce\modules\admin\widgets
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

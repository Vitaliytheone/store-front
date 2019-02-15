<?php

namespace sommerce\modules\admin\widgets;

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
        ob_start();
        echo $this->content;
        $output = ob_get_contents();
        ob_end_clean();
        return $output;
    }
}

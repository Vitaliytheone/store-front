<?php

namespace sommerce\modules\admin\widgets;

use yii\base\Widget;

/**
 * Class ChatsWidget
 * @package sommerce\modules\admin\widgets
 */
class ChatsWidget extends Widget
{
    /** @var string */
    public $content = '';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
    }

    /**
     * {@inheritdoc}
     * @return string
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

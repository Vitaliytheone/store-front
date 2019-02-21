<?php

namespace sommerce\widgets;

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
        return $this->content;
    }
}

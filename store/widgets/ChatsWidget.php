<?php

namespace store\widgets;

use yii\base\Widget;

/**
 * Class ChatsWidget
 * @package store\modules\admin\widgets
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

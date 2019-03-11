<?php

namespace console\controllers\control_panel;


/**
 * Class SystemController
 * @package console\controllers\my
 */
class SystemController extends CustomController
{
    public $start;

    public function options($actionID)
    {
        return ['start'];
    }
}

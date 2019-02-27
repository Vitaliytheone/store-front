<?php

namespace console\controllers\control_panel;

use Yii;
use console\components\MainMigrateController;

/**
 * Class CustomMigrateController
 * @package console\controllers\control_panel
 */
class CustomMigrateController extends MainMigrateController
{
    public function init()
    {
        $this->frontendPath = Yii::getAlias('@control_panel/config');

        parent::init();
    }
}
<?php
namespace console\components;

use console\components\traits\MainControllerTrait;
use \yii\console\controllers\MigrateController;

/**
 * Class MigrateController
 * @package console\controllers\my
 */
class MainMigrateController extends MigrateController
{
    use MainControllerTrait;
}
<?php
namespace console\controllers\my;

use console\components\MainController;
use Yii;
use yii\console\Controller;

/**
 * Class CustomController
 * @package console\controllers\my
 */
class CustomController extends MainController
{
    public function init()
    {
        $this->frontendPath = Yii::getAlias('@my/config');

        parent::init(); // TODO: Change the autogenerated stub
    }
}
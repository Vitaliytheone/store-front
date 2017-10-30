<?php
namespace common\components;

use Yii;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\View;

/**
 * MainController controller
 */
class MainController extends Controller
{
    /**
     * Activate js module
     * @param string $name
     * @param array $options
     */
    public function addModule($name, $options = [])
    {
        $this->getView()->registerJs('window.modules.' . $name . ' = ' . Json::encode($options) . ';', View::POS_END);
    }
}
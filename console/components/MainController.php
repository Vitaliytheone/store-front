<?php
namespace console\components;

use Yii;
use yii\console\Controller;

/**
 * Class MainController
 * @package console\components
 */
class MainController extends Controller
{
    public $frontendPath;

    public function init()
    {
        $commonPath = Yii::getAlias('@common/config');
        $consolePath = Yii::getAlias('@console/config');

        $frontendPath = $this->frontendPath;

        $params = array_merge(
            require($commonPath . '/params.php'),
            file_exists($commonPath . '/params-local.php') ? require($commonPath . '/params-local.php') : [],
            require($consolePath . '/params.php'),
            file_exists($consolePath . '/params-local.php') ? require($consolePath . '/params-local.php') : [],
            require($frontendPath . '/params.php'),
            file_exists($frontendPath . '/params-local.php') ? require($frontendPath . '/params-local.php') : []
        );

        Yii::$app->params = $params;

        $db = array_merge(
            require($frontendPath. '/db.php'),
            file_exists($frontendPath . '/db-local.php') ? require($frontendPath . '/db-local.php') : []
        );

        foreach ($db as $name => $options) {
            Yii::$app->set($name, $options);
        }

        parent::init(); // TODO: Change the autogenerated stub
    }
}
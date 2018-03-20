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
            require_once($commonPath . '/params.php'),
            file_exists($commonPath . '/params-local.php') ? require_once($commonPath . '/params-local.php') : [],
            require_once($consolePath. '/params.php'),
            file_exists($consolePath . '/params-local.php') ? require_once($consolePath . '/params-local.php') : [],
            require_once($frontendPath. '/params.php'),
            file_exists($frontendPath . '/params-local.php') ? require_once($frontendPath . '/params-local.php') : []
        );

        $db = array_merge(
            require_once($frontendPath. '/db.php'),
            file_exists($frontendPath . '/db-local.php') ? require_once($frontendPath . '/db-local.php') : []
        );

        Yii::$app->params = $params;

        foreach ($db as $name => $options) {
            Yii::$app->set($name, $options);
        }

        parent::init(); // TODO: Change the autogenerated stub
    }
}
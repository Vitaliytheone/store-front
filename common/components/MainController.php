<?php
namespace common\components;

use common\helpers\UrlHelper;
use Yii;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\View;

/**
 * MainController controller
 */
class MainController extends Controller
{
    /**
     * @var bool whether to enable domain validation for the post actions in this controller.
     */
    public $enableDomainValidation = true;

    /**
     * Activate js module
     * @param string $name
     * @param array $options
     */
    public function addModule($name, $options = [])
    {
        $this->getView()->registerJs('window.modules.' . $name . ' = ' . Json::encode($options) . ';', View::POS_END);
    }

    public function beforeAction($action)
    {
        $result = parent::beforeAction($action);

        // Validate post requests
        if (Yii::$app->request->isPost && $this->enableDomainValidation) {
            if (!UrlHelper::isOurCall()) {
                throw new ForbiddenHttpException(Yii::t('yii', 'Unable to verify your data submission.'));
            }
        }

        return $result;
    }
}
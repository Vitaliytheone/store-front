<?php
namespace common\components;

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
            $refererDomain = !empty($_SERVER['HTTP_REFERER']) ? parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST) : null;

            if (empty($refererDomain) || mb_strtolower($refererDomain) !== mb_strtolower($_SERVER['HTTP_HOST'])) {
                throw new ForbiddenHttpException(Yii::t('yii', 'Unable to verify your data submission.'));
            }
        }

        return $result;
    }
}
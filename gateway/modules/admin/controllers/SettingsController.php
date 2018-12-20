<?php
namespace admin\controllers;

use admin\controllers\traits\settings\PagesTrait;
use admin\controllers\traits\settings\PaymentsTrait;
use admin\controllers\traits\settings\ThemesTrait;
use Codeception\Lib\Interfaces\ActiveRecord;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\ContentNegotiator;
use yii\filters\AjaxFilter;
use \yii\filters\VerbFilter;

/**
 * Settings controller for the `admin` module
 */
class SettingsController extends CustomController
{
    use ThemesTrait;
    use PaymentsTrait;
    use PagesTrait;

    public function behaviors()
    {
        $parentBehaviors = parent::behaviors();
        return $parentBehaviors + [
            'ajax' => [
                'class' => AjaxFilter::class,
                'only' => ['theme-get-style', 'theme-get-data', 'theme-update-style', 'payments-toggle-active']
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'customize-theme' => ['GET'],
                    'theme-get-style' => ['GET'],
                    'theme-get-data' => ['GET'],
                    'theme-update-style' => ['POST'],
                    'payments-toggle-active' => ['POST'],
                ],
            ],
            'content' => [
                'class' => ContentNegotiator::class,
                'only' => ['theme-update-style', 'payments-toggle-active'],
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        // Disabled csrf validation for some ajax actions
        if (in_array($action->id, [
            'update-theme',
            'theme-update-style',
            'payments-toggle-active',
        ])) {
            $this->enableCsrfValidation = false;
        }
        // Add custom JS modules

        return parent::beforeAction($action);
    }

    /**
     * @param int $id
     * @param ActiveRecord $class
     * @return ActiveRecord
     * @throws NotFoundHttpException
     */
    protected function _findModel($id, $class)
    {
        if (empty($id) || !($model = $class::findOne($id))) {
            throw new NotFoundHttpException();
        }

        return $model;
    }
}

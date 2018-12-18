<?php
namespace admin\controllers;

use admin\controllers\traits\settings\ThemesTrait;
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

    public function behaviors()
    {
        $parentBehaviors = parent::behaviors();
        return $parentBehaviors + [
            'ajax' => [
                'class' => AjaxFilter::class,
                'only' => ['theme-get-style', 'theme-get-data', 'theme-update-style']
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'customize-theme' => ['GET'],
                    'theme-get-style' => ['GET'],
                    'theme-get-data' => ['GET'],
                    'theme-update-style' => ['POST'],
                ],
            ],
            'content' => [
                'class' => ContentNegotiator::class,
                'only' => ['theme-update-style'],
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
            'theme-update-style'
        ])) {
            $this->enableCsrfValidation = false;
        }
        // Add custom JS modules

        return parent::beforeAction($action);
    }
}

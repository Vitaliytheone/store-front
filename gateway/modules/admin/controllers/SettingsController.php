<?php
namespace admin\controllers;

use admin\controllers\traits\settings\FilesTrait;
use admin\controllers\traits\settings\PaymentsTrait;
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
    use FilesTrait;
    use PaymentsTrait;

    public function behaviors()
    {
        $parentBehaviors = parent::behaviors();
        return $parentBehaviors + [
            'ajax' => [
                'class' => AjaxFilter::class,
                'only' => [
                    'payments-toggle-active',
                    'update-file',
                    'rename-file',
                    'delete-file',
                    'create-file',
                    'upload-file',
                ]
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'payments-toggle-active' => ['POST'],
                    'update-file' => ['POST'],
                    'rename-file' => ['POST'],
                    'delete-file' => ['POST'],
                    'create-file' => ['POST'],
                    'upload-file' => ['POST'],
                ],
            ],
            'content' => [
                'class' => ContentNegotiator::class,
                'only' => [
                    'payments-toggle-active',
                    'update-file',
                    'rename-file',
                    'delete-file',
                    'create-file',
                    'upload-file',
                ],
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
     * @param int|array $id
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

<?php

namespace superadmin\controllers;

use control_panel\components\SuperAccessControl;
use superadmin\helpers\DashboardServices;
use superadmin\helpers\DashboardBlocks;
use Yii;
use yii\filters\AjaxFilter;
use yii\filters\ContentNegotiator;
use yii\web\HttpException;
use \yii\web\Response;
use \yii\filters\VerbFilter;

/**
 * Default controller for the `superadmin` module
 */
class DashboardController extends CustomController
{
    public $activeTab = 'dashboard';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => SuperAccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ]
                ],
            ],
            'ajax' => [
                'class' => AjaxFilter::class,
                'only' => ['block', 'balance']
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'index' => ['GET'],
                    'block'=> ['GET'],
                    'blance' => ['GET'],
                ],
            ],
            'content' => [
                'class' => ContentNegotiator::class,
                'only' => ['block', 'balance'],
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
        ];
    }

    /**
     * Renders the index view for the module
     * @return string
     * @throws \ReflectionException
     */
    public function actionIndex()
    {
        $this->view->title = Yii::t('app/superadmin', 'pages.title.dashboard');
        return $this->render('index', [
            'dashboardBlocks' => DashboardBlocks::getBlocks(),
            'activePanel' => DashboardBlocks::BLOCK_STORES,
            'dashboardServices' => DashboardServices::getServices(),
        ]);
    }

    /**
     * Get dashboard blocks
     * @param $name
     * @return array
     * @throws \ReflectionException
     */
    public function actionBlock($name)
    {
        $panel = DashboardBlocks::getBlock($name);
        return $panel->getEntities();
    }

    /**
     * Get service balance
     * @param string $serviceName
     * @return mixed|\yii\console\Response|Response
     * @throws HttpException
     * @internal param $service
     */
    public function actionBalance($serviceName)
    {
        $service = DashboardServices::getService($serviceName);

        if (!$service) {
            throw new HttpException(404, Yii::t('app/superadmin', 'error.service.not_found'));
        }

        $balance = $service->getBalance();
        if ($service->hasError()) {
            return [
                'status' => 'error',
                'message' => $service->getError()->getMessage()
            ];
        }

        return [
            'status' => 'success',
            'data' => $balance
        ];
    }
}

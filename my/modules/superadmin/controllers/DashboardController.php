<?php

namespace my\modules\superadmin\controllers;

use my\components\SuperAccessControl;
use my\modules\superadmin\helpers\DashboardServices;
use my\modules\superadmin\helpers\DashboardBlocks;
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
    public $layout = 'superadmin_v2.php';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => SuperAccessControl::className(),
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
                'class' => VerbFilter::className(),
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
     */
    public function actionIndex()
    {
        $this->view->title = Yii::t('app/superadmin', 'pages.title.dashboard');
        return $this->render('index', [
            'dashboardBlocks' => DashboardBlocks::getBlocks(),
            'activePanel' => DashboardBlocks::BLOCK_PANELS,
            'dashboardServices' => DashboardServices::getServices(),
        ]);
    }

    /**
     * Get dashboard blocks
     * @param string $name
     * @return array
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

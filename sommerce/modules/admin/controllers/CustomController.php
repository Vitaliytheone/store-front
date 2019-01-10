<?php

namespace sommerce\modules\admin\controllers;

use common\models\stores\StoreAdminAuth;
use Yii;
use yii\filters\AccessControl;
use yii\web\User;

/**
 * Custom controller for the `admin` module
 */
class CustomController extends AdminController
{
    public $activeTab;

    public $layout = '@admin/views/layouts/main.php';

    /**
     * Allowed controllers list for current admin
     * [
     *      'admin/orders',
     *      'admin/settings',
     * ]
     * @var array
     */
    private $_allowedControllers = [];

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        /** @var User $user */
        $user = Yii::$app->user;

        /** @var StoreAdminAuth $identity */
        $identity = $user->getIdentity();

        if ($identity) {
            $this->_allowedControllers = $identity->getAllowedControllers();
        }
        
        $this->addModule('adminLayout');

        return parent::beforeAction($action);
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'controllers' => $this->_allowedControllers,
                    ],
                ],
                'denyCallback' => function($rule, $action){
                    Yii::$app->user->loginRequired();
                }
            ],
        ];
    }

    /**
     * Render content
     * @param string $view
     * @param array $params
     * @return string
     */
    public function render($view, $params = [])
    {
        $messages = Yii::$app->session->getFlash('messages');

        if (!empty($messages)) {
            $this->addModule('adminNotifyLayout', [
                'messages' => $messages
            ]);
        }

        return parent::render($view, $params);
    }
}

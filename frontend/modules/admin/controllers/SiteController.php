<?php

namespace frontend\modules\admin\controllers;

use common\models\stores\StoreAdmins;
use frontend\modules\admin\components\Url;
use frontend\modules\admin\models\forms\LoginForm;
use Yii;
use yii\filters\AccessControl;
use common\components\MainController;
use yii\helpers\ArrayHelper;
use yii\web\User;

/**
 * Site controller for the `admin` module
 */
class SiteController extends MainController
{
    /**
     * Layout for login pages
     * @var string
     */
    public $layout = '@admin/views/layouts/login.php';

    /**
     * Current first allowed redirect path
     * @var string
     */
    private $_loggedInRedirectUrl;

    /**
     * Instance of current user
     * @var User
     */
    private $_user;

    /**
     * Redirect order list
     * @var array
     */
    private $_redirectList= [
        'orders',
        'payments',
        'products',
        'settings',
        StoreAdmins::DEFAULT_CONTROLLER,
    ];

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'frontend\modules\admin\components\CustomErrorAction',
                'layout' => '@admin/views/layouts/main.php',
                'view' => '@admin/views/error/404',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        $this->_user = Yii::$app->user;

        if (!$this->_user->isGuest) {
            $this->_makeRedirectUrl();
        }

        return parent::beforeAction($action); // TODO: Change the autogenerated stub
    }

    /**
     * Make redirect url for logged in user
     * If exist return Url, and it is allowed, use it.
     * Else use first allowed controller from _redirectList list
     * @return string
     */
    private function _makeRedirectUrl()
    {
        /** @var StoreAdmins $adminModel */
        $adminModel = $this->_user->getIdentity();
        $allowedControllers = $adminModel->getAllowedControllersNames();

        // Try to redirect by `return url`
        $returnUrl = $this->_user->getReturnUrl();
        if ($returnUrl) {

            $path = parse_url($returnUrl, PHP_URL_PATH);
            $parsedPath = explode('/', trim($path, '/'));

            if ($path && isset($parsedPath[1])) {

                $returnController = $parsedPath[1];

                // Check if $returnController allowed
                if (in_array($returnController, $allowedControllers)) {
                    $this->_loggedInRedirectUrl = ltrim($returnUrl, '/admin');

                    return $this->_loggedInRedirectUrl;
                }
            }
        }

        // Else Try to find first allowed action in redirect orders list
        $firstAllowedController = StoreAdmins::DEFAULT_CONTROLLER;
        foreach ($this->_redirectList as $redirect) {
            if (false !== array_search($redirect, $allowedControllers)) {
                $firstAllowedController = $redirect;
                break;
            }
        }

        $this->_loggedInRedirectUrl = '/' . $firstAllowedController;
        return $this->_loggedInRedirectUrl;
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index'],
                'rules' => [
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['index'],
                        'allow' => false,
                        'roles' => ['@'],
                    ]
                ],

                /**
                 * Redirect logged in user to
                 * allowed controller from $_redirectOrderList
                 */
                'denyCallback' => function($rule, $action){

                   $this->redirect(Url::toRoute($this->_loggedInRedirectUrl));

                }
            ],
        ];
    }

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $this->view->title = Yii::t('admin', 'login.sign_in_page_title');

        $form = new LoginForm();

        if (
            $form->load(Yii::$app->getRequest()->post()) &&
            $form->validate() &&
            $form->login()
        ) {
            $this->redirect(Url::toRoute($this->_loggedInRedirectUrl));
        }

        return $this->render('signin', [
            'form' => $form,
        ]);
    }
}

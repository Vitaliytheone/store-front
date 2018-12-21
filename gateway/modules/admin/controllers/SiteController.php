<?php
namespace admin\controllers;

use admin\components\Url;
use admin\models\forms\LoginForm;
use admin\models\forms\SuperLoginFom;
use Yii;
use yii\filters\AccessControl;
use yii\web\User;

/**
 * Site controller for the `admin` module
 */
class SiteController extends CustomController
{
    /**
     * Layout for login pages
     * @var string
     */
    public $layout = '@admin/views/layouts/login.php';

    /**
     * Instance of current user
     * @var User
     */
    private $_user;

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'admin\components\CustomErrorAction',
                'layout' => '@admin/views/layouts/main.php',
                'view' => '@admin/views/error/404',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['index', 'super-login', 'frozen'],
                'rules' => [
                    [
                        'actions' => ['index', 'super-login', 'frozen'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => false,
                        'roles' => ['?'],
                    ],

                ],

                /**
                 * Redirect logged in user to
                 * allowed controller from $_redirectOrderList
                 */
                'denyCallback' => function($rule, $action){
                    if (!Yii::$app->user->isGuest) {
                        $this->redirect(Url::toRoute('/settings'));
                    } else {
                        $this->redirect(Url::toRoute('/'));
                    }
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

        if ($form->load(Yii::$app->getRequest()->post()) && $form->login()) {
            if ($this->gateway->isInactive()) {
                return $this->redirect(Url::toRoute('/frozen'));
            }

            $this->redirect(Url::toRoute('/settings'));
        }

        return $this->render('signin', [
            'form' => $form,
        ]);
    }

    /**
     * @param $token
     * @return \yii\web\Response
     * @throws \Exception
     */
    public function actionSuperLogin($token)
    {
        $form = new SuperLoginFom();

        if (!$form->login($token)) {
            return $this->redirect(Url::toRoute('/'));
        }

        $this->redirect(Url::toRoute('/settings'));
    }

    /**
     * Render store frozen page for guest and non-superadmins
     * @return string
     */
    public function actionFrozen()
    {

        if (!$this->gateway->isInactive()) {
            return $this->redirect(Url::toRoute('/'));
        }

        return $this->renderPartial('frozen');
    }

    /**
     * Logout action.
     * @return string
     */
    public function actionLogout()
    {
        $user = Yii::$app->user;

        $user->logout();
        $user->loginRequired();
    }
}

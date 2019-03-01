<?php

namespace sommerce\modules\admin\controllers;

use admin\controllers\traits\PagesTrait;
use common\components\ActiveForm;
use common\components\response\CustomResponse;
use common\helpers\SiteHelper;
use common\models\sommerce\Pages;
use sommerce\helpers\UiHelper;
use sommerce\modules\admin\components\CustomUser;
use sommerce\modules\admin\models\forms\EditPageForm;
use sommerce\modules\admin\models\search\PagesSearch;
use sommerce\modules\admin\models\search\UrlsSearch;
use Yii;
use yii\filters\AjaxFilter;
use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;


/**
 * Class PagesController
 * @package sommerce\modules\admin\controllers\
 */
class PagesController extends CustomController
{
    use PagesTrait;

    protected $exceptCsrfValidation = [
        'delete-page',
        'duplicate-page',
        'update-blocks',
        'block-upload',
        'update-theme',
        'theme-update-style',
        // Page editor react post-requests
        'draft',
        'publish',
        'set-product',
        'set-package',
        'set-image',
        'unset-image',
    ];

    /**
     * @param \yii\base\Action $action
     * @return bool
     */
    public function beforeAction($action)
    {
        if (ArrayHelper::isIn($action->id, $this->exceptCsrfValidation)) {
            $this->enableCsrfValidation = false;
        }

        return parent::beforeAction($action);
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $parentBehaviors = parent::behaviors();
        return $parentBehaviors + [
            'ajax' => [
                'class' => AjaxFilter::class,
                'only' => ['create-page'],
            ],
            'content' => [
                'class' => ContentNegotiator::class,
                'only' => ['create-page', 'update-page', 'delete-page', 'duplicate-page'],
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'index' => ['GET'],
                    'create-page' => ['POST'],
                    'update-page' => ['POST'],
                    'delete-page' => ['POST'],
                    'duplicate-page' => ['POST']
                ],
            ],
                'ajaxApi' => [
                    'class' => ContentNegotiator::class,
                    'only' => [
                        // Pages trait
                        'get-page',
                        'get-pages',
                        'draft',
                        'publish',
                        'get-products',
                        'get-product',
                        'set-product',
                        'set-package',
                        'set-image',
                        'unset-image',
                        'get-images',
                    ],
                    'formats' => [
                        'application/json' => CustomResponse::FORMAT_AJAX_API,
                    ],
                ],
        ];
    }

    /**
     * Default page
     * @return string
     */
    public function actionIndex()
    {
       $this->view->title = Yii::t('admin', "settings.pages_page_title");

        $urlsModel = new UrlsSearch();
        $urlsModel->setStore($this->store);
        $existingUrls = $urlsModel->searchUrls();

        $this->addModule('adminPages', [
            'existingUrls' => $existingUrls,
            'confirm_message' => Yii::t('admin', 'pages.confirm_message')
        ]);

        $search = new PagesSearch();
        $search->setStore($this->store);
        $pages = $search->searchPages();

        return $this->render('index', [
            'host' => SiteHelper::hostUrl($this->store->ssl),
            'pages' => $pages
        ]);
    }

    /**
     * Add page
     * @return array
     */
    public function actionCreatePage()
    {
        $request = Yii::$app->request;
        $model = new EditPageForm();

        /**x
         * @var $user CustomUser
         */
        $user = Yii::$app->user;

        $model->setUser($user);

        if ($model->load($request->post()) && $model->add()) {
            return [
                'status' => 'success',
                'errors' => null
            ];
        }

        return [
            'status' => 'error',
            'message' => ActiveForm::firstError($model)
        ];


    }

    /**
     * Edit page
     * @param int $id
     * @return array
     */
    public function actionUpdatePage($id)
    {

        $request = Yii::$app->request;
        $page = $this->findModel($id);
        $model = new EditPageForm();
        $model->setPage($page);

        /**x
         * @var $user CustomUser
         */
        $user = Yii::$app->user;

        $model->setUser($user);

        if ($model->load($request->post()) && $model->edit()) {
            return [
                'status' => 'success',
                'errors' => null
            ];
        }

        return [
            'status' => 'error',
            'message' => ActiveForm::firstError($model)
        ];
    }

    /**
     * Delete page
     * @return array
     */
    public function actionDeletePage()
    {
        $request = Yii::$app->request;
        $page = $this->findModel($request->post('id'));
        $model = new EditPageForm();
        $model->setPage($page);
        /**x
         * @var $user CustomUser
         */
        $user = Yii::$app->user;

        $model->setUser($user);

        if ($model->delete()) {
            return [
                'status' => 'success',
                'errors' => null
            ];
        }

        return [
            'status' => 'error',
            'message' => ActiveForm::firstError($model)
        ];
    }

    /**
     * Duplicate page
     * @return array
     */
    public function actionDuplicatePage()
    {
        $request = Yii::$app->request;
        $page = $this->findModel($request->post('id'));
        $model = new EditPageForm();
        $model->setPage($page);
        /**x
         * @var $user CustomUser
         */
        $user = Yii::$app->user;

        $model->setUser($user);

        if ($model->duplicate($request->post('url'))) {
            UiHelper::message(Yii::t('admin', 'pages.is_duplicated'));
            return [
                'status' => 'success',
                'errors' => null
            ];
        }

        return [
            'status' => 'error',
            'message' => ActiveForm::firstError($model)
        ];
    }

    /**
     * @param int $id
     * @return Pages
     * @throws NotFoundHttpException
     */
    protected function findModel($id) {
        $model = Pages::findOne($id);

        if (!$model) {
            throw new NotFoundHttpException();
        }

        return $model;
    }
}

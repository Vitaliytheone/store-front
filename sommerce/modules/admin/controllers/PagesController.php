<?php

namespace sommerce\modules\admin\controllers;

use common\components\ActiveForm;
use sommerce\modules\admin\components\CustomUser;
use sommerce\modules\admin\models\forms\AddPageForm;
use Yii;
use yii\filters\AjaxFilter;
use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use yii\web\Response;


/**
 * Class PagesController
 * @package sommerce\modules\admin\controllers\
 */
class PagesController extends CustomController
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'ajax' => [
                'class' => AjaxFilter::class,
                'only' => ['create-page'],
            ],
            'content' => [
                'class' => ContentNegotiator::class,
                'only' => ['create-page',],
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'index' => ['GET'],
                    'create-page' => ['POST']
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
       $this->addModule('adminPages');
      //  $search = new PagesOldSearch();
       // $search->setStore($this->store);
       // $pages = $search->searchPages();


        //return $this->render('pages', [
           // 'pages' => $pages,
       // ]);

       /*return $this->render('index', [
           // 'pages' => $pages,
       ]);*/

        return $this->render('index', [

        ]);


    }

    /**
     * Add page
     * @return array
     */
    public function actionCreatePage()
    {
        $request = Yii::$app->request;
        $model = new AddPageForm();

        /**x
         * @var $panel CustomUser
         */
        $user = Yii::$app->user;

        $model->setUser($user);

        if ($model->load($request->post()) && $model->save()) {
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
}

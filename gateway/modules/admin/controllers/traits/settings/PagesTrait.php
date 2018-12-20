<?php
namespace admin\controllers\traits\settings;

use common\components\ActiveForm;
use common\models\gateway\Pages;
use gateway\controllers\CommonController;
use gateway\helpers\UiHelper;
use admin\components\Url;
use admin\models\forms\EditFilePageForm;
use admin\models\forms\EditPageForm;
use admin\models\forms\SavePageForm;
use admin\models\search\PagesSearch;
use admin\models\search\UrlsSearch;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Class PagesTrait
 * @property CommonController $this
 * @package admin\controllers
 */
trait PagesTrait {

    /**
     * Settings pages
     * @return string
     */
    public function actionPages()
    {
        $this->view->title = Yii::t('admin', "settings.pages_page_title");
        $this->addModule('adminPages');

        $search = new PagesSearch();
        $search->setGateway($this->gateway);

        return $this->render('pages', [
            'pages' => $search->search(),
        ]);
    }

    /**
     * Create page
     * @return string|Response
     */
    public function actionCreatePage()
    {
        $this->view->title = Yii::t('admin', "settings.pages_create_page");

        $pageForm = new SavePageForm();
        $pageForm->setUser(Yii::$app->user);

        $urlsModel = new UrlsSearch();
        $urlsModel->setStore($this->gateway);
        $exitingUrls = $urlsModel->searchUrls();

        $this->addModule('adminPageEdit', [
            'urls' => $exitingUrls,
            'url_error' => $pageForm->getFirstError('url'),
        ]);

        return $this->render('edit_page', [
            'pageForm' => $pageForm,
            'isNewPage' => 1,
            'url' => $this->gateway->getBaseSite(),
            'actionUrl' => Url::toRoute('/settings/new-page'),
        ]);
    }

    /**
     * Update page
     * @param $id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdatePage($id)
    {
        $this->view->title = Yii::t('admin', "settings.pages_edit_page");

        $page = $this->_findModel($id, Pages::class);
        $pageForm = new EditPageForm();
        $pageForm->setUser(Yii::$app->user);
        $pageForm->setPage($page);

        $this->addModule('adminPageEdit', [
            'url_error' => $pageForm->getFirstError('url'),
            'pageId' => $pageForm->getPage()->id,
        ]);

        $view = $pageForm->getPage()->template == 'file' ? 'edit_page_file' : 'edit_page';

        return $this->render($view, [
            'pageForm' => $pageForm,
            'isNewPage' => 0,
            'storeUrl' => Yii::$app->store->getInstance()->getBaseSite(),
            'actionUrl' => Url::toRoute(['/settings/edit-page', 'id' => $pageForm->getPage()->id]),
        ]);
    }

    /**
     * Create/Update page AJAX action
     * @param $id
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionEditPage($id = null)
    {
        $request = Yii::$app->getRequest();
        $response = Yii::$app->getResponse();
        $response->format = Response::FORMAT_JSON;

        if (!$request->isAjax) {
            exit;
        }

        $page = Pages::findOne($id);
        if ($page->template == 'file') {
            $pageForm = new EditFilePageForm();
        } else {
            $pageForm = new EditPageForm();
        }

        $pageForm->setUser(Yii::$app->user);

        if (!$pageForm->edit($request->post(), $id)) {
            return [
                'success' => false,
                'message' => ActiveForm::firstError($pageForm, true)
            ];
        };

        return [
            'success' => true,
            'message' => Yii::t('admin', 'settings.pages_message_updated'),
            'id' => $pageForm->getPage()->id,
        ];
    }


    /**
     * Create page AJAX action
     * @param $id
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionNewPage($id = null)
    {
        $request = Yii::$app->getRequest();
        $response = Yii::$app->getResponse();
        $response->format = Response::FORMAT_JSON;

        if (!$request->isAjax) {
            exit;
        }

        $pageForm = new SavePageForm();
        $pageForm->setUser(Yii::$app->user);

        if (!$pageForm->edit($request->post(), $id)) {
            return [
                'success' => false,
                'message' => ActiveForm::firstError($pageForm, true)
            ];
        };

        return [
            'success' => true,
            'message' => Yii::t('admin', 'settings.pages_message_updated'),
            'id' => $pageForm->getPage()->id,
        ];
    }

    /**
     * Virtual deleting `page`
     * @param $id
     * @return array
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function actionDeletePage($id)
    {
        $request = Yii::$app->getRequest();
        $response = Yii::$app->getResponse();
        $response->format = Response::FORMAT_JSON;

        if (!$request->isAjax) {
            exit;
        }

        $pageModel = Pages::findOne($id);
        if (!$pageModel) {
            throw new NotFoundHttpException();
        }

        if (!Pages::canDelete($pageModel->toArray())) {
            throw new ForbiddenHttpException();
        }

        $pageModel->deleteVirtual();

        UiHelper::message(Yii::t('admin', 'settings.pages_message_deleted'));

        return [true];
    }
}
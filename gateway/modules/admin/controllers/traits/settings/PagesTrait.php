<?php
namespace admin\controllers\traits\settings;

use common\components\ActiveForm;
use common\models\gateway\Pages;
use gateway\controllers\CommonController;
use gateway\helpers\UiHelper;
use admin\components\Url;
use admin\models\forms\EditPageForm;
use admin\models\search\PagesSearch;
use admin\models\search\UrlsSearch;
use Yii;
use yii\web\BadRequestHttpException;
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

        $pageForm = new EditPageForm();
        $pageForm->setUser(Yii::$app->user);
        $pageForm->setGateway($this->gateway);

        $urlsModel = new UrlsSearch();
        $urlsModel->setGateway($this->gateway);

        $this->addModule('adminPageEdit', [
            'urls' => $urlsModel->search(),
            'url_error' => $pageForm->getFirstError('url'),
        ]);

        return $this->render('edit_page', [
            'pageForm' => $pageForm,
            'isNewPage' => 1,
            'siteUrl' => $this->gateway->getBaseSite(),
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

        /**
         * @var Pages $pageModel
         */
        $page = $this->_findModel($id, Pages::class);

        $pageForm = new EditPageForm();
        $pageForm->setUser(Yii::$app->user);
        $pageForm->setGateway($this->gateway);
        $pageForm->setPage($page);

        $this->addModule('adminPageEdit', [
            'url_error' => $pageForm->getFirstError('url'),
            'pageId' => $pageForm->getPage()->id,
        ]);

        return $this->render('edit_page', [
            'pageForm' => $pageForm,
            'isNewPage' => 0,
            'siteUrl' => $this->gateway->getBaseSite(),
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

        /**
         * @var Pages $pageModel
         */
        $page = $this->_findModel($id, Pages::class);

        $pageForm = new EditPageForm();
        $pageForm->setGateway($this->gateway);
        $pageForm->setPage($page);
        $pageForm->setUser(Yii::$app->user);

        if (!$pageForm->load($request->post()) || !$pageForm->save()) {
            return [
                'success' => false,
                'message' => ActiveForm::firstError($pageForm, true)
            ];
        };

        UiHelper::message(Yii::t('admin', 'settings.pages_message_updated'));

        return [
            'success' => true,
            'message' => Yii::t('admin', 'settings.pages_message_updated'),
            'redirect' => Url::toRoute(['/settings/update-page', 'id' => $pageForm->getPage()->id]),
        ];
    }


    /**
     * Create page AJAX action
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionNewPage()
    {
        $request = Yii::$app->getRequest();

        $pageForm = new EditPageForm();
        $pageForm->setGateway($this->gateway);
        $pageForm->setUser(Yii::$app->user);
        if (!$pageForm->load($request->post()) || !$pageForm->save()) {
            return [
                'success' => false,
                'message' => ActiveForm::firstError($pageForm, true)
            ];
        };

        UiHelper::message(Yii::t('admin', 'settings.pages_message_updated'));

        return [
            'success' => true,
            'message' => Yii::t('admin', 'settings.pages_message_updated'),
            'redirect' => Url::toRoute(['/settings/update-page', 'id' => $pageForm->getPage()->id]),
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
        /**
         * @var Pages $pageModel
         */
        $pageModel = $this->_findModel($id, Pages::class);

        if (!$pageModel->can(Pages::CAN_DELETE)) {
            return [false];
        }

        $pageModel->delete();

        UiHelper::message(Yii::t('admin', 'settings.pages_message_deleted'));

        return [true];
    }
}
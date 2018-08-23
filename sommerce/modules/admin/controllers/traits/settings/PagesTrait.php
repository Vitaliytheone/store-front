<?php
namespace sommerce\modules\admin\controllers\traits\settings;

use common\components\ActiveForm;
use common\models\store\ActivityLog;
use common\models\store\Pages;
use common\models\stores\StoreAdminAuth;
use sommerce\controllers\CommonController;
use sommerce\helpers\UiHelper;
use sommerce\modules\admin\components\Url;
use sommerce\modules\admin\models\forms\EditPageForm;
use sommerce\modules\admin\models\search\PagesSearch;
use sommerce\modules\admin\models\search\UrlsSearch;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Class PagesTrait
 * @property CommonController $this
 * @package sommerce\modules\admin\controllers
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
        $search->setStore($this->store);
        $pages = $search->searchPages();

        return $this->render('pages', [
            'pages' => $pages,
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
        $pageForm->setPage(new Pages());

        $urlsModel = new UrlsSearch();
        $urlsModel->setStore($this->store);
        $exitingUrls = $urlsModel->searchUrls();

        $this->addModule('adminPageEdit', [
            'urls' => $exitingUrls,
            'url_error' => $pageForm->getFirstError('url'),
        ]);

        return $this->render('edit_page', [
            'pageForm' => $pageForm,
            'isNewPage' => $pageForm->getPage()->isNewRecord,
            'storeUrl' => Yii::$app->store->getInstance()->getBaseSite(),
            'actionUrl' => Url::toRoute('/settings/edit-page'),
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

        $pageForm = new EditPageForm();
        $pageForm->setUser(Yii::$app->user);
        $pageForm->setPage(Pages::findOne($id));

        if (!$pageForm->getPage() instanceof Pages) {
            throw new NotFoundHttpException();
        }

        $this->addModule('adminPageEdit', [
            'url_error' => $pageForm->getFirstError('url'),
            'pageId' => $pageForm->getPage()->id,
        ]);

        return $this->render('edit_page', [
            'pageForm' => $pageForm,
            'isNewPage' => $pageForm->getPage()->isNewRecord,
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

        $pageForm = new EditPageForm();
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

        $pageModel->deleteVirtual();

        /** @var StoreAdminAuth $identity */
        $identity = Yii::$app->user->getIdentity(false);

        ActivityLog::log($identity, ActivityLog::E_SETTINGS_PAGES_PAGE_DELETED);

        UiHelper::message(Yii::t('admin', 'settings.pages_message_deleted'));

        return [true];
    }
}
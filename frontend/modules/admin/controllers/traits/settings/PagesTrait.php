<?php
namespace frontend\modules\admin\controllers\traits\settings;

use common\models\store\ActivityLog;
use common\models\store\Pages;
use common\models\stores\StoreAdminAuth;
use frontend\helpers\UiHelper;
use frontend\modules\admin\components\Url;
use frontend\modules\admin\models\forms\EditPageForm;
use frontend\modules\admin\models\search\PagesSearch;
use frontend\modules\admin\models\search\UrlsSearch;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Class PagesTrait
 * @property Controller $this
 * @package frontend\modules\admin\controllers
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

        $pages = (new PagesSearch())->searchPages();

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

        $request = Yii::$app->getRequest();

        $pageModel = new EditPageForm();
        $pageModel->setUser(Yii::$app->user);

        if ($pageModel->create($request->post())) {
            UiHelper::message(Yii::t('admin', 'settings.pages_message_created'));
            return $this->redirect(Url::toRoute('/settings/pages'));
        }

        $urlsModel = new UrlsSearch();
        $exitingUrls = $urlsModel->searchUrls();

        $this->addModule('adminPageEdit', [
            'urls' => $exitingUrls,
            'url_error' => $pageModel->getFirstError('url'),
        ]);

        return $this->render('edit_page', [
            'page' => $pageModel,
            'storeUrl' => Yii::$app->store->getInstance()->domain,
        ]);
    }

    /**
     * Edit page
     * @param $id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionEditPage($id)
    {
        $this->view->title = Yii::t('admin', "settings.pages_edit_page");

        $request = Yii::$app->getRequest();

        $pageModel = EditPageForm::findOne($id);
        $pageModel->setUser(Yii::$app->user);

        if (!$pageModel) {
            throw new NotFoundHttpException();
        }

        if ($pageModel->edit($request->post())) {
            UiHelper::message(Yii::t('admin', 'settings.pages_message_updated'));
            return $this->redirect(Url::toRoute('/settings/pages'));
        }

        $this->addModule('adminPageEdit', [
            'url_error' => $pageModel->getFirstError('url'),
        ]);

        return $this->render('edit_page', [
            'page' => $pageModel,
            'storeUrl' => Yii::$app->store->getInstance()->domain,
        ]);
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
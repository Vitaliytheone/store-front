<?php
namespace frontend\modules\admin\controllers\traits\settings;

use common\components\ActiveForm;
use common\models\store\Navigation;
use frontend\helpers\UiHelper;
use frontend\models\search\NavigationSearch;
use frontend\modules\admin\models\forms\EditNavigationForm;
use frontend\modules\admin\models\forms\UpdatePositionsNavigationForm;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotAcceptableHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Class NavigationTrait
 * @property Controller $this
 * @package frontend\modules\admin\controllers
 */
trait NavigationTrait {

    /**
     * Settings navigation
     * @return string
     */
    public function actionNavigation()
    {
        $this->view->title = Yii::t('admin', 'settings.nav_page_title');

        $model = new EditNavigationForm();
        $search = new NavigationSearch();

        return $this->render('navigation', [
            'linkTypes' => $model::linkTypes(),
            'navTree' => $search->getTree(),
        ]);
    }

    /**
     * Create new Navigation item
     * @return array
     */
    public function actionCreateNav()
    {
        $request = Yii::$app->getRequest();
        $response = Yii::$app->getResponse();
        $response->format = Response::FORMAT_JSON;

        if (!$request->isAjax) {
            exit;
        }

        $model = new EditNavigationForm();

        if (!$model->load($request->post()) || !$model->save()) {
            return ['error' => ActiveForm::firstError($model)];
        }

        UiHelper::message(Yii::t('admin', 'settings.nav_message_created'));

        return [
            'product' => $model->getAttributes(),
        ];
    }

    /**
     * Update exiting Navigation item
     * @param $id
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionUpdateNav($id)
    {
        $request = Yii::$app->getRequest();
        $response = Yii::$app->getResponse();
        $response->format = Response::FORMAT_JSON;

        if (!$request->isAjax) {
            exit;
        }

        $model = EditNavigationForm::findOne($id);

        if (!$model) {
            throw new NotFoundHttpException();
        }

        if (!$model->load($request->post()) || !$model->save()) {
            return ['error' => ActiveForm::firstError($model)];
        }

        UiHelper::message(Yii::t('admin', 'settings.nav_message_updated'));

        return ['model' => $model->getAttributes()];
    }

    /**
     * Return Navigation item AJAX action
     * @param $id
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionGetNav($id)
    {
        $request = Yii::$app->getRequest();
        $response = Yii::$app->getResponse();
        $response->format = Response::FORMAT_JSON;

        if (!$request->isAjax) {
            exit;
        }

        $model = Navigation::findOne($id);

        if (!$model) {
            throw new NotFoundHttpException();
        }

        return ['model' => $model->getAttributes()];
    }

    /**
     * Delete Navigation item AJAX action
     * Mark package as deleted
     * @param $id
     * @return array
     * @throws NotAcceptableHttpException
     * @throws NotFoundHttpException
     */
    public function actionDeleteNav($id)
    {
        $request = Yii::$app->getRequest();
        $response = Yii::$app->getResponse();
        $response->format = Response::FORMAT_JSON;

        if (!$request->isAjax) {
            exit;
        }

        $model = Navigation::findOne($id);

        if (!$model) {
            throw new NotFoundHttpException();
        }

        $model->deleteVirtual();

        UiHelper::message(Yii::t('admin', 'settings.nav_message_deleted'));

        return [true];
    }

    /**
     * Update Navigation items positions after drag&drop AJAX action
     * @return array
     * @throws BadRequestHttpException
     */
    public function actionUpdatePositionsNav()
    {
        $request = Yii::$app->getRequest();
        $response = Yii::$app->getResponse();
        $response->format = Response::FORMAT_JSON;

        if (!$request->isAjax) {
            exit;
        }

        $model = new UpdatePositionsNavigationForm();
        if (!$model->updatePositions($request->post())) {
            throw new BadRequestHttpException();
        }

        return [true];
    }

}
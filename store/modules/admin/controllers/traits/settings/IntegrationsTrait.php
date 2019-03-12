<?php

namespace store\modules\admin\controllers\traits\settings;

use my\components\ActiveForm;
use store\modules\admin\components\Url;
use store\modules\admin\models\forms\EditIntegrationForm;
use store\modules\admin\models\search\IntegrationsSearch;
use Yii;
use common\models\stores\Stores;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Trait IntegrationsTrait
 * @package store\modules\admin\controllers\traits\settings
 */
trait IntegrationsTrait
{
    /**
     * @return mixed
     */
    public function actionIntegrations()
    {
        $this->view->title = Yii::t('admin', "settings.integrations_page_title");
        $this->addModule('adminIntegrations');

        /**
         * @var Stores $store
         */
        $store = Yii::$app->store->getInstance();

        $search = new IntegrationsSearch();
        $search->setStore($store);

        return $this->render('integrations', [
            'integrations' => $search->search(),
        ]);
    }

    /**
     * @param $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionEditIntegration($id)
    {
        $this->addModule('adminIntegrations');
        $request = Yii::$app->request;
        $store = Yii::$app->store->getInstance();

        $search = new IntegrationsSearch();
        $search->setStore($store);
        $search->setIntegrationId($id);

        if ($request->method === 'GET') {
            $integration = $search->search();
            if (!$integration) {
                throw new NotFoundHttpException();
            }

            $this->view->title = $integration['name'];
            return $this->render('layouts/integrations/_edit_integration', [
                'integration' => $integration,
            ]);
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        $form = new EditIntegrationForm();
        $form->setStoreIntegration($id);
        $form->load($request->post(), '');

        if (!$form->save()) {
            return [
                'status' => 'error',
                'error' => ActiveForm::firstError($form),
            ];
        }

        return [
            'status' => 'success',
            'redirect' => Url::toRoute('/settings/integrations'),
        ];
    }

    /**
     * @param $id
     * @return array
     * @throws BadRequestHttpException
     * @throws \Throwable
     */
    public function actionIntegrationsToggleActive($id)
    {
        $request = Yii::$app->getRequest();

        $active = $request->post('active', null);

        if (is_null($active)) {
            throw new BadRequestHttpException();
        }

        $form = new EditIntegrationForm();
        $form->setStoreIntegration($id);

        return [
            'active' => $form->setActive($active|0),
        ];
    }
}

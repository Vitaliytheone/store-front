<?php

namespace sommerce\modules\admin\controllers\traits\settings;

use sommerce\modules\admin\components\Url;
use sommerce\modules\admin\models\forms\EditIntegrationForm;
use sommerce\modules\admin\models\search\IntegrationsSearch;
use Yii;
use common\models\stores\Stores;
use yii\web\BadRequestHttpException;
use yii\web\Response;

/**
 * Trait IntegrationsTrait
 * @package sommerce\modules\admin\controllers\traits\settings
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
     */
    public function actionEditIntegration($id)
    {
        $request = Yii::$app->request;
        $store = Yii::$app->store->getInstance();

        $search = new IntegrationsSearch();
        $search->setStore($store);
        $search->setIntegrationId($id);

        if ($request->method === 'GET') {
            $integration = $search->search();
            $this->view->title = $integration['name'];

            return $this->render('integrations', [
                'editPage' => true,
                'integration' => $integration,
            ]);
        }

        $form = new EditIntegrationForm();
        $form->setStoreIntegration($id);
        $form->load($request->post(), '');
        $form->save();
        return $this->redirect(Url::toRoute('/settings/integrations'));
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
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!$request->isAjax) {
            exit;
        }

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

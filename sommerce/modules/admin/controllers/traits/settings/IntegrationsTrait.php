<?php

namespace sommerce\modules\admin\controllers\traits\settings;

use sommerce\modules\admin\models\search\IntegrationsSearch;
use Yii;
use common\models\stores\Stores;

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

    public function actionEditIntegration($id)
    {
        $request = Yii::$app->request;

        if ($request->method === 'GET') {
            return $this->render('layout/integrations/_edit_integration');
        }
    }

    public function actionIntegrationsToggleActive($code)
    {
        //TODO: realization of enable/disable integrations
    }
}
<?php
namespace sommerce\modules\admin\controllers\traits\settings;

use common\components\ActiveForm;
use sommerce\helpers\UiHelper;
use sommerce\modules\admin\models\forms\CreateProviderForm;
use Yii;
use sommerce\modules\admin\models\forms\ProvidersListForm;
use sommerce\modules\admin\models\search\ProvidersSearch;
use yii\web\Controller;
use yii\web\Response;

/**
 * Class ProvidersTrait
 * @property Controller $this
 * @package sommerce\modules\admin\controllers
 */
trait ProvidersTrait {

    /**
     * Settings providers
     * @return string
     */
    public function actionProviders()
    {
        $this->view->title = 'Settings providers';

        $search = new ProvidersSearch();

        $this->addModule('adminProviders');

        $model = new ProvidersListForm();
        $model->setStore(Yii::$app->store->getInstance());
        $model->setUser(Yii::$app->user);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            UiHelper::message(Yii::t('admin', 'settings.message_provider_updated'));
            return $this->refresh();
        }

        return $this->render('providers', [
            'providers' => $search->search()
        ]);
    }

    /**
     * Create provider
     *
     * @access public
     * @return mixed
     */
    public function actionCreateProvider()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new CreateProviderForm();
        $model->setStore(Yii::$app->store->getInstance());
        $model->setUser(Yii::$app->user);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            UiHelper::message(Yii::t('admin', 'settings.message_provider_created'));
            return [
                'status' => 'success',
            ];
        } else {
            return [
                'status' => 'error',
                'message' => ActiveForm::firstError($model)
            ];
        }
    }
}
<?php

namespace my\modules\superadmin\controllers;

use common\models\panels\PanelDomains;
use common\models\panels\SuperAdmin;
use common\models\panels\SuperAdminToken;
use my\components\ActiveForm;
use my\helpers\StringHelper;
use my\helpers\Url;
use common\models\panels\Project;
use my\modules\superadmin\models\forms\ChangeDomainForm;
use my\modules\superadmin\models\forms\EditExpiryForm;
use my\modules\superadmin\models\forms\EditProjectForm;
use my\modules\superadmin\models\forms\EditProvidersForm;
use my\modules\superadmin\models\forms\UpgradePanelForm;
use my\modules\superadmin\models\search\PanelsSearch;
use my\modules\superadmin\models\search\StoresSearch;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Controller StoresController for the `superadmin` module
 */
class StoresController extends CustomController
{
    public $activeTab = 'stores';

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $this->view->title = Yii::t('app/superadmin', 'pages.title.stores');

        $params = Yii::$app->request->get();
        $params['child'] = 1;
        $storesSearch = new StoresSearch();
        $storesSearch->setParams($params);

        $filters = $storesSearch->getParams();
        $status = ArrayHelper::getValue($filters, 'status');

        return $this->render('index', [
            'stores' => $storesSearch->search(),
            'navs' => $storesSearch->navs(),
            'status' => is_numeric($status) ? (int)$status : $status,
            'filters' => $storesSearch->getParams()
        ]);
    }
}

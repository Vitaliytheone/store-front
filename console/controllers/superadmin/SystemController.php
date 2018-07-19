<?php

namespace console\controllers\superadmin;

use common\helpers\CurrencyHelper;
use common\models\panels\Project;
use console\controllers\my\CustomController;

/**
 * Class SystemController
 * @package console\controllers\superadmin
 */
class SystemController extends CustomController
{
    public function actionSetUpCurrency()
    {
        $models = Project::find()->all();
        foreach ($models as $model) {
            $model->currency_code = CurrencyHelper::getCurrencyCodeById($model->currency);
            $model->update(false);
        }
    }
}
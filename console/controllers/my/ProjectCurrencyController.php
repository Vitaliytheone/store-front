<?php

namespace console\controllers\my;


use common\helpers\CurrencyHelper;
use common\models\panels\AdditionalServices;
use yii\db\Query;

class ProjectCurrencyController extends CustomController
{

    public function actionChangeCurrency()
    {
        $additionalServices = AdditionalServices::find()->where(['type' => 1])->all();

        foreach ($additionalServices as $key => $service) {
            $panel = (new Query())
                ->select('currency')
                ->from(DB_PANELS.'.project')
                ->where(['site' => $service->name])
                ->one();

            $service->currency = CurrencyHelper::getCurrencyCodeById($panel['currency']);
            $service->save();
        }
    }
}

<?php

namespace frontend\modules\admin\controllers;

use common\models\stores\StoreAdminAuth;
use common\models\stores\Stores;
use Yii;
use frontend\controllers\CommonController;
use yii\web\User;

/**
 * Class AdminController
 * @package frontend\modules\admin\controllers
 */
class AdminController extends CommonController
{
    /** @inheritdoc */
    public function beforeAction($action)
    {
        /** @var User $user */
        $user = Yii::$app->user;

        /** @var Stores $store */
        $store = Yii::$app->store->getInstance();

        // Frozen/terminated store routine
        if ($store->isInactive() && !$user->isGuest) {

            /** @var StoreAdminAuth $identity */
            $identity = $user->getIdentity();

            if (!$identity->isSuper()) {
                $user->logout();
            }
        }

        return parent::beforeAction($action); // TODO: Change the autogenerated stub
    }
}
